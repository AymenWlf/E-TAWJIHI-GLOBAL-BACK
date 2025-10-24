<?php

namespace App\Command;

use App\Entity\Establishment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(
    name: 'app:import-french-schools',
    description: 'Import French business schools data from JSON file',
)]
class ImportFrenchSchoolsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SluggerInterface $slugger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Importing French Business Schools');

        // Read JSON file
        $jsonFile = __DIR__ . '/../../../schools_data_enriched_detailed.json';

        if (!file_exists($jsonFile)) {
            $io->error('JSON file not found: ' . $jsonFile);
            return Command::FAILURE;
        }

        $jsonContent = file_get_contents($jsonFile);
        $data = json_decode($jsonContent, true);

        if (!$data || !isset($data['schools'])) {
            $io->error('Invalid JSON format or missing schools data');
            return Command::FAILURE;
        }

        $schools = $data['schools'];
        $imported = 0;
        $skipped = 0;

        foreach ($schools as $schoolData) {
            try {
                // Check if establishment already exists
                $existingEstablishment = $this->entityManager
                    ->getRepository(Establishment::class)
                    ->findOneBy(['slug' => $schoolData['slug']]);

                if ($existingEstablishment) {
                    $io->note("Skipping existing establishment: {$schoolData['name']}");
                    $skipped++;
                    continue;
                }

                // Create new establishment
                $establishment = new Establishment();

                // Basic information
                $establishment->setName($schoolData['name']);
                $establishment->setNameFr($schoolData['nameFr'] ?? $schoolData['name']);
                $establishment->setSlug($schoolData['slug']);
                $establishment->setType($schoolData['type']);
                $establishment->setCountry($schoolData['country']);
                $establishment->setCity($schoolData['city']);

                // Descriptions
                $establishment->setDescription($schoolData['description']);
                $establishment->setDescriptionFr($schoolData['descriptionFr'] ?? $schoolData['description']);
                $establishment->setMission($schoolData['mission'] ?? null);
                $establishment->setMissionFr($schoolData['missionFr'] ?? null);

                // Academic information
                $establishment->setFoundedYear($schoolData['foundedYear'] ?? null);
                $establishment->setRating($schoolData['rating'] ?? null);
                $establishment->setStudents($schoolData['students'] ?? null);
                $establishment->setPrograms($schoolData['programs'] ?? null);

                // Tuition information
                $establishment->setTuitionMin($schoolData['tuitionMin'] ?? null);
                $establishment->setTuitionMax($schoolData['tuitionMax'] ?? null);
                $establishment->setTuitionCurrency($schoolData['tuitionCurrency'] ?? null);

                // Rankings
                $establishment->setAcceptanceRate($schoolData['acceptanceRate'] ?? null);
                $establishment->setQsRanking($schoolData['qsRanking'] ?? null);
                $establishment->setTimesRanking($schoolData['timesRanking'] ?? null);
                $establishment->setWorldRanking($schoolData['worldRanking'] ?? null);

                // Programs and deadlines
                $establishment->setPopularPrograms($schoolData['popularPrograms'] ?? null);
                if (isset($schoolData['applicationDeadline'])) {
                    $establishment->setApplicationDeadline(new \DateTime($schoolData['applicationDeadline']));
                }

                // Scholarships
                $establishment->setScholarships($schoolData['scholarships'] ?? false);
                $establishment->setScholarshipTypes($schoolData['scholarshipTypes'] ?? null);
                $establishment->setScholarshipDescription($schoolData['scholarshipDescription'] ?? null);

                // Services
                $establishment->setHousing($schoolData['housing'] ?? false);
                $establishment->setLanguage($schoolData['language'] ?? null);
                $establishment->setAccommodation($schoolData['accommodation'] ?? false);
                $establishment->setCareerServices($schoolData['careerServices'] ?? false);
                $establishment->setLanguageSupport($schoolData['languageSupport'] ?? false);

                // Application settings
                $establishment->setAidvisorRecommended($schoolData['aidvisorRecommended'] ?? false);
                $establishment->setEasyApply($schoolData['easyApply'] ?? false);
                $establishment->setUniversityType($schoolData['universityType'] ?? 'A');
                $establishment->setCommissionRate($schoolData['commissionRate'] ?? null);
                $establishment->setFreeApplications($schoolData['freeApplications'] ?? null);
                $establishment->setVisaSupport($schoolData['visaSupport'] ?? null);

                // Contact information
                $establishment->setWebsite($schoolData['website'] ?? null);
                $establishment->setEmail($schoolData['email'] ?? null);
                $establishment->setPhone($schoolData['phone'] ?? null);
                $establishment->setAddress($schoolData['address'] ?? null);

                // Accreditations
                $establishment->setAccreditations($schoolData['accreditations'] ?? null);

                // Status
                $establishment->setFeatured($schoolData['featured'] ?? false);
                $establishment->setSponsored($schoolData['sponsored'] ?? false);
                $establishment->setIsActive($schoolData['isActive'] ?? true);

                // Admission requirements
                $establishment->setAdmissionRequirements($schoolData['admissionRequirements'] ?? null);
                $establishment->setAdmissionRequirementsFr($schoolData['admissionRequirementsFr'] ?? null);

                // Fees
                $establishment->setApplicationFee($schoolData['applicationFee'] ?? null);
                $establishment->setApplicationFeeCurrency($schoolData['applicationFeeCurrency'] ?? null);
                $establishment->setLivingCosts($schoolData['livingCosts'] ?? null);
                $establishment->setLivingCostsCurrency($schoolData['livingCostsCurrency'] ?? null);

                // Logo
                $establishment->setLogo($schoolData['logo'] ?? null);

                $this->entityManager->persist($establishment);
                $imported++;

                $io->text("Imported: {$schoolData['name']}");
            } catch (\Exception $e) {
                $io->error("Error importing {$schoolData['name']}: " . $e->getMessage());
                continue;
            }
        }

        $this->entityManager->flush();

        $io->success("Import completed successfully!");
        $io->table(
            ['Metric', 'Count'],
            [
                ['Imported', $imported],
                ['Skipped', $skipped],
                ['Total Processed', $imported + $skipped]
            ]
        );

        return Command::SUCCESS;
    }
}
