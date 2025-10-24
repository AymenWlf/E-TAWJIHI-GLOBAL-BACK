<?php

namespace App\Command;

use App\Entity\Establishment;
use App\Repository\EstablishmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-enriched-schools',
    description: 'Import enriched schools data from JSON file',
)]
class ImportEnrichedSchoolsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EstablishmentRepository $establishmentRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Path to the JSON file', 'schools_data_complete_enriched.json')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Run without making changes to the database')
            ->addOption('skip-existing', null, InputOption::VALUE_NONE, 'Skip schools that already exist in the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getOption('file');
        $dryRun = $input->getOption('dry-run');
        $skipExisting = $input->getOption('skip-existing');

        $io->title('Importation des Écoles Enrichies');

        // Vérifier que le fichier existe
        if (!file_exists($filePath)) {
            $io->error("Le fichier {$filePath} n'existe pas.");
            return Command::FAILURE;
        }

        // Lire le fichier JSON
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (!$data || !isset($data['schools'])) {
            $io->error('Format de fichier JSON invalide.');
            return Command::FAILURE;
        }

        $schools = $data['schools'];
        $totalSchools = count($schools);

        $io->info("Fichier trouvé: {$filePath}");
        $io->info("Nombre total d'écoles: {$totalSchools}");

        if ($dryRun) {
            $io->warning('Mode DRY-RUN activé - Aucune modification ne sera apportée à la base de données');
        }

        $imported = 0;
        $skipped = 0;
        $errors = 0;

        $progressBar = $io->createProgressBar($totalSchools);
        $progressBar->start();

        foreach ($schools as $index => $schoolData) {
            try {
                $progressBar->advance();

                // Vérifier si l'école existe déjà
                if ($skipExisting) {
                    $existingSchool = $this->establishmentRepository->findOneBy(['name' => $schoolData['name']]);
                    if ($existingSchool) {
                        $skipped++;
                        continue;
                    }
                }

                if (!$dryRun) {
                    $establishment = $this->createEstablishmentFromData($schoolData);
                    $this->entityManager->persist($establishment);
                }

                $imported++;
            } catch (\Exception $e) {
                $errors++;
                $io->error("Erreur lors de l'importation de {$schoolData['name']}: " . $e->getMessage());
            }
        }

        $progressBar->finish();
        $io->newLine(2);

        if (!$dryRun && $imported > 0) {
            $this->entityManager->flush();
            $io->success("Importation terminée avec succès!");
        }

        // Afficher les statistiques
        $io->section('Statistiques d\'importation');
        $io->table(
            ['Métrique', 'Valeur'],
            [
                ['Total d\'écoles dans le fichier', $totalSchools],
                ['Écoles importées', $imported],
                ['Écoles ignorées (déjà existantes)', $skipped],
                ['Erreurs', $errors],
                ['Mode', $dryRun ? 'DRY-RUN' : 'IMPORTATION RÉELLE']
            ]
        );

        // Afficher les métadonnées du fichier
        if (isset($data['metadata'])) {
            $metadata = $data['metadata'];
            $io->section('Métadonnées du fichier');
            $io->table(
                ['Propriété', 'Valeur'],
                [
                    ['Dernière mise à jour', $metadata['lastUpdated'] ?? 'N/A'],
                    ['Source', $metadata['source'] ?? 'N/A'],
                    ['Pays couverts', implode(', ', $metadata['countries'] ?? [])],
                    ['Types d\'écoles', count($metadata['types'] ?? []) . ' types'],
                    ['Écoles françaises', $metadata['frenchSchools'] ?? 'N/A'],
                    ['Écoles internationales', $metadata['internationalSchools'] ?? 'N/A']
                ]
            );
        }

        return Command::SUCCESS;
    }

    private function createEstablishmentFromData(array $data): Establishment
    {
        $establishment = new Establishment();

        // Informations de base
        $establishment->setName($data['name']);
        $establishment->setCountry($data['country']);
        $establishment->setCity($data['city']);
        $establishment->setType($data['type']);
        $establishment->setDescription($data['description'] ?? '');

        // Slug
        if (isset($data['slug'])) {
            $establishment->setSlug($data['slug']);
        } else {
            $establishment->setSlug($this->generateSlug($data['name']));
        }

        // Informations académiques
        if (isset($data['foundedYear'])) {
            $establishment->setFoundedYear($data['foundedYear']);
        }

        if (isset($data['rating'])) {
            $establishment->setRating($data['rating']);
        }

        if (isset($data['students'])) {
            $establishment->setStudents($data['students']);
        }

        if (isset($data['programs'])) {
            $establishment->setPrograms($data['programs']);
        }

        // Informations financières
        if (isset($data['tuitionMin'])) {
            $establishment->setTuitionMin($data['tuitionMin']);
        }

        if (isset($data['tuitionMax'])) {
            $establishment->setTuitionMax($data['tuitionMax']);
        }

        if (isset($data['tuitionCurrency'])) {
            $establishment->setTuitionCurrency($data['tuitionCurrency']);
        }

        if (isset($data['acceptanceRate'])) {
            $establishment->setAcceptanceRate($data['acceptanceRate']);
        }

        // Classements
        if (isset($data['qsRanking'])) {
            $establishment->setQsRanking($data['qsRanking']);
        }

        if (isset($data['timesRanking'])) {
            $establishment->setTimesRanking($data['timesRanking']);
        }

        if (isset($data['worldRanking'])) {
            $establishment->setWorldRanking($data['worldRanking']);
        }

        // Programmes populaires
        if (isset($data['popularPrograms']) && is_array($data['popularPrograms'])) {
            $establishment->setPopularPrograms($data['popularPrograms']);
        }

        // Informations pratiques
        if (isset($data['website'])) {
            $establishment->setWebsite($data['website']);
        }

        if (isset($data['email'])) {
            $establishment->setEmail($data['email']);
        }

        if (isset($data['phone'])) {
            $establishment->setPhone($data['phone']);
        }

        if (isset($data['address'])) {
            $establishment->setAddress($data['address']);
        }

        // Bourses et logement
        if (isset($data['scholarships'])) {
            $establishment->setScholarships($data['scholarships']);
        }

        if (isset($data['housing'])) {
            $establishment->setHousing($data['housing']);
        }

        if (isset($data['accommodation'])) {
            $establishment->setAccommodation($data['accommodation']);
        }

        // Services
        if (isset($data['careerServices'])) {
            $establishment->setCareerServices($data['careerServices']);
        }

        if (isset($data['languageSupport'])) {
            $establishment->setLanguageSupport($data['languageSupport']);
        }

        // Langues
        if (isset($data['language'])) {
            $establishment->setLanguage($data['language']);
        }

        // Recommandations
        if (isset($data['aidvisorRecommended'])) {
            $establishment->setAidvisorRecommended($data['aidvisorRecommended']);
        }

        if (isset($data['easyApply'])) {
            $establishment->setEasyApply($data['easyApply']);
        }

        if (isset($data['featured'])) {
            $establishment->setFeatured($data['featured']);
        }

        // Type d'université et commission
        if (isset($data['universityType'])) {
            $establishment->setUniversityType($data['universityType']);
        }

        if (isset($data['commissionRate'])) {
            $establishment->setCommissionRate($data['commissionRate']);
        }

        if (isset($data['freeApplications'])) {
            $establishment->setFreeApplications($data['freeApplications']);
        }

        if (isset($data['visaSupport'])) {
            $establishment->setVisaSupport($data['visaSupport']);
        }

        // Accréditations
        if (isset($data['accreditations']) && is_array($data['accreditations'])) {
            $establishment->setAccreditations($data['accreditations']);
        }

        // Statut actif
        $establishment->setIsActive($data['isActive'] ?? true);

        // Dates
        $now = new \DateTime();
        $establishment->setCreatedAt($now);
        $establishment->setUpdatedAt($now);

        return $establishment;
    }

    private function generateSlug(string $name): string
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        // Vérifier l'unicité du slug
        $originalSlug = $slug;
        $counter = 1;

        while ($this->establishmentRepository->findOneBy(['slug' => $slug])) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
