<?php

namespace App\Command;

use App\Entity\Program;
use App\Entity\Establishment;
use App\Repository\ProgramRepository;
use App\Repository\EstablishmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-french-programs-type',
    description: 'Updates all French programs and establishments to type B',
)]
class UpdateFrenchProgramsTypeCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProgramRepository $programRepository,
        private EstablishmentRepository $establishmentRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Run without making changes')
            ->addOption('establishments-only', null, InputOption::VALUE_NONE, 'Update only establishments')
            ->addOption('programs-only', null, InputOption::VALUE_NONE, 'Update only programs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $establishmentsOnly = $input->getOption('establishments-only');
        $programsOnly = $input->getOption('programs-only');

        $io->title('Mise à jour des programmes et établissements français vers le type B');

        if ($dryRun) {
            $io->note('Mode dry-run activé - aucune modification ne sera effectuée.');
        }

        $updatedEstablishments = 0;
        $updatedPrograms = 0;

        // Update French establishments to type B
        if (!$programsOnly) {
            $io->section('Mise à jour des établissements français');

            $frenchEstablishments = $this->establishmentRepository->createQueryBuilder('e')
                ->where('e.country = :country')
                ->setParameter('country', 'France')
                ->getQuery()
                ->getResult();

            $io->info(sprintf('Trouvé %d établissements français.', count($frenchEstablishments)));

            foreach ($frenchEstablishments as $establishment) {
                if ($establishment->getUniversityType() !== 'B') {
                    $io->writeln(sprintf(
                        '  📝 Mise à jour de %s (type: %s → B)',
                        $establishment->getName(),
                        $establishment->getUniversityType() ?? 'null'
                    ));

                    if (!$dryRun) {
                        $establishment->setUniversityType('B');
                        $this->entityManager->persist($establishment);
                    }
                    $updatedEstablishments++;
                } else {
                    $io->writeln(sprintf('  ✅ %s est déjà de type B', $establishment->getName()));
                }
            }
        }

        // Update French programs to type B
        if (!$establishmentsOnly) {
            $io->section('Mise à jour des programmes français');

            $frenchPrograms = $this->programRepository->createQueryBuilder('p')
                ->join('p.establishment', 'e')
                ->where('e.country = :country')
                ->setParameter('country', 'France')
                ->getQuery()
                ->getResult();

            $io->info(sprintf('Trouvé %d programmes français.', count($frenchPrograms)));

            foreach ($frenchPrograms as $program) {
                if ($program->getUniversityType() !== 'B') {
                    $io->writeln(sprintf(
                        '  📝 Mise à jour de %s (type: %s → B)',
                        $program->getName(),
                        $program->getUniversityType() ?? 'null'
                    ));

                    if (!$dryRun) {
                        $program->setUniversityType('B');
                        $this->entityManager->persist($program);
                    }
                    $updatedPrograms++;
                } else {
                    $io->writeln(sprintf('  ✅ %s est déjà de type B', $program->getName()));
                }
            }
        }

        if (!$dryRun) {
            $this->entityManager->flush();
        }

        $io->success(sprintf(
            'Mise à jour terminée: %d établissements et %d programmes mis à jour vers le type B',
            $updatedEstablishments,
            $updatedPrograms
        ));

        return Command::SUCCESS;
    }
}
