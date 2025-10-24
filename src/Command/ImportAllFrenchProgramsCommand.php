<?php

namespace App\Command;

use App\Entity\Program;
use App\Entity\Establishment;
use App\Entity\ProgramRequirement;
use App\Repository\ProgramRepository;
use App\Repository\EstablishmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(
    name: 'app:import-all-french-programs',
    description: 'Imports all French programs from the complete JSON file',
)]
class ImportAllFrenchProgramsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProgramRepository $programRepository,
        private EstablishmentRepository $establishmentRepository,
        private SluggerInterface $slugger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Run without making changes')
            ->addOption('skip-existing', null, InputOption::VALUE_NONE, 'Skip programs that already exist');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $skipExisting = $input->getOption('skip-existing');

        $io->title('Importation de tous les programmes français');

        // Vérifier si le fichier existe
        $jsonFile = 'french_schools_programs_complete.json';
        if (!file_exists($jsonFile)) {
            $io->error("Le fichier {$jsonFile} n'existe pas.");
            return Command::FAILURE;
        }

        // Charger les données JSON
        $jsonContent = file_get_contents($jsonFile);
        $data = json_decode($jsonContent, true);

        if (!$data || !isset($data['programs'])) {
            $io->error('Format de fichier JSON invalide.');
            return Command::FAILURE;
        }

        $programs = $data['programs'];
        $io->info(sprintf('Trouvé %d programmes à importer.', count($programs)));

        if ($dryRun) {
            $io->note('Mode dry-run activé - aucune modification ne sera effectuée.');
        }

        $importedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        foreach ($programs as $index => $programData) {
            try {
                $io->writeln(sprintf(
                    'Traitement du programme %d/%d: %s',
                    $index + 1,
                    count($programs),
                    $programData['name']
                ));

                // Vérifier si le programme existe déjà
                if ($skipExisting) {
                    $establishmentName = $programData['establishment'];
                    if (is_array($establishmentName)) {
                        $establishmentName = $establishmentName['name'] ?? $establishmentName['title'] ?? 'Unknown';
                    }

                    $existingProgram = $this->programRepository->findOneBy([
                        'name' => $programData['name'],
                        'establishment' => $this->findEstablishment($establishmentName)
                    ]);

                    if ($existingProgram) {
                        $io->writeln('  ⏭️  Programme existant ignoré');
                        $skippedCount++;
                        continue;
                    }
                }

                // Créer le programme
                $program = $this->createProgram($programData);

                if (!$dryRun) {
                    $this->entityManager->persist($program);

                    // Créer les exigences si elles existent
                    if (isset($programData['requirements'])) {
                        $this->createRequirements($program, $programData['requirements']);
                    }
                }

                $importedCount++;
                $io->writeln('  ✅ Programme créé');
            } catch (\Exception $e) {
                $io->writeln(sprintf('  ❌ Erreur: %s', $e->getMessage()));
                $errorCount++;
            }
        }

        if (!$dryRun) {
            $this->entityManager->flush();
        }

        $io->success(sprintf(
            'Importation terminée: %d programmes importés, %d ignorés, %d erreurs',
            $importedCount,
            $skippedCount,
            $errorCount
        ));

        return Command::SUCCESS;
    }

    private function findEstablishment(string $establishmentName): ?Establishment
    {
        // Essayer de trouver par nom exact
        $establishment = $this->establishmentRepository->findOneBy(['name' => $establishmentName]);

        if ($establishment) {
            return $establishment;
        }

        // Essayer de trouver par slug
        $slug = $this->slugger->slug($establishmentName)->lower()->toString();
        $establishment = $this->establishmentRepository->findOneBy(['slug' => $slug]);

        if ($establishment) {
            return $establishment;
        }

        // Essayer de trouver par correspondance partielle
        $establishments = $this->establishmentRepository->findAll();
        foreach ($establishments as $est) {
            if (
                stripos($est->getName(), $establishmentName) !== false ||
                stripos($establishmentName, $est->getName()) !== false
            ) {
                return $est;
            }
        }

        return null;
    }

    private function createProgram(array $data): Program
    {
        $program = new Program();

        $program->setName($data['name']);
        $program->setNameFr($data['nameFr'] ?? $data['name']);
        $program->setDescription($data['description'] ?? '');
        $program->setDescriptionFr($data['descriptionFr'] ?? $data['description'] ?? '');
        $program->setDegree($data['degree'] ?? 'Master\'s');
        $program->setDuration($data['duration'] ?? '1 year');
        $program->setStudyType($data['studyType'] ?? 'on-campus');
        $program->setStudyLevel($data['studyLevel'] ?? 'graduate');
        $program->setSubject($data['subject'] ?? 'Business Administration');
        $program->setField($data['field'] ?? 'Business');
        $program->setLanguage($data['language'] ?? 'English');
        $program->setTuition($data['tuition'] ?? 'Contact school');
        $program->setTuitionAmount($data['tuitionAmount'] ?? '0');
        $program->setTuitionCurrency($data['tuitionCurrency'] ?? 'EUR');
        $program->setStartDate(new \DateTime($data['startDate'] ?? '2025-09-01'));
        $program->setIsActive($data['isActive'] ?? true);
        $program->setSlug($data['slug'] ?? $this->generateSlug($data['name']));
        $program->setCreatedAt(new \DateTime());
        $program->setUpdatedAt(new \DateTime());

        // Associer à l'établissement
        $establishmentName = $data['establishment'];
        if (is_array($establishmentName)) {
            // Si c'est un objet, extraire le nom
            $establishmentName = $establishmentName['name'] ?? $establishmentName['title'] ?? 'Unknown';
        }

        $establishment = $this->findEstablishment($establishmentName);
        if ($establishment) {
            $program->setEstablishment($establishment);
        } else {
            throw new \Exception(sprintf('Établissement non trouvé: %s', $establishmentName));
        }

        return $program;
    }

    private function createRequirements(Program $program, array $requirementsData): void
    {
        // Créer les exigences académiques
        if (isset($requirementsData['academic']) && is_array($requirementsData['academic'])) {
            foreach ($requirementsData['academic'] as $reqData) {
                $requirement = new ProgramRequirement();
                $requirement->setProgram($program);
                $requirement->setType('academic');
                $requirement->setName($reqData['name'] ?? '');
                $requirement->setDescription($reqData['description'] ?? '');
                $requirement->setIsRequired($reqData['required'] ?? true);
                $requirement->setSubtype($reqData['type'] ?? 'document');
                $requirement->setCreatedAt(new \DateTime());
                $requirement->setUpdatedAt(new \DateTime());

                $this->entityManager->persist($requirement);
            }
        }

        // Créer les exigences de langue
        if (isset($requirementsData['english']) && is_array($requirementsData['english'])) {
            foreach ($requirementsData['english'] as $reqData) {
                $requirement = new ProgramRequirement();
                $requirement->setProgram($program);
                $requirement->setType('language');
                $requirement->setName($reqData['name'] ?? '');
                $requirement->setDescription($reqData['description'] ?? '');
                $requirement->setIsRequired($reqData['required'] ?? true);
                $requirement->setSubtype($reqData['type'] ?? 'test');
                $requirement->setCreatedAt(new \DateTime());
                $requirement->setUpdatedAt(new \DateTime());

                $this->entityManager->persist($requirement);
            }
        }

        // Créer les exigences de documents
        if (isset($requirementsData['documents']) && is_array($requirementsData['documents'])) {
            foreach ($requirementsData['documents'] as $reqData) {
                $requirement = new ProgramRequirement();
                $requirement->setProgram($program);
                $requirement->setType('document');
                $requirement->setName($reqData['name'] ?? '');
                $requirement->setDescription($reqData['description'] ?? '');
                $requirement->setIsRequired($reqData['required'] ?? true);
                $requirement->setSubtype($reqData['type'] ?? 'document');
                $requirement->setCreatedAt(new \DateTime());
                $requirement->setUpdatedAt(new \DateTime());

                $this->entityManager->persist($requirement);
            }
        }
    }

    private function generateSlug(string $name): string
    {
        $slug = $this->slugger->slug($name)->lower()->toString();

        // Vérifier l'unicité
        $i = 1;
        $originalSlug = $slug;
        while ($this->programRepository->findOneBy(['slug' => $slug])) {
            $slug = $originalSlug . '-' . $i++;
        }

        return $slug;
    }
}
