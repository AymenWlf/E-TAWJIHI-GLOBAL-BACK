<?php

namespace App\Command;

use App\Entity\Establishment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-french-schools-type',
    description: 'Update type for French business schools to be more specific',
)]
class UpdateFrenchSchoolsTypeCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Updating French Schools Type');

        // Define specific types for each school
        $schoolsData = [
            'EM Lyon' => 'Grande école de commerce',
            'SKEMA Business School' => 'Grande école de commerce',
            'NEOMA Business School' => 'Grande école de commerce',
            'AUDENCIA Business School' => 'Grande école de commerce',
            'IESEG School of Management' => 'Grande école de commerce'
        ];

        $updated = 0;
        foreach ($schoolsData as $schoolName => $newType) {
            $establishment = $this->entityManager->getRepository(Establishment::class)
                ->findOneBy(['name' => $schoolName]);

            if ($establishment) {
                $oldType = $establishment->getType();
                $establishment->setType($newType);

                $this->entityManager->persist($establishment);
                $updated++;

                $io->success("Updated {$schoolName}: {$oldType} → {$newType}");
            } else {
                $io->warning("School not found: {$schoolName}");
            }
        }

        $this->entityManager->flush();

        $io->success("Successfully updated type for {$updated} French business schools.");

        return Command::SUCCESS;
    }
}
