<?php

namespace App\Command;

use App\Entity\Program;
use App\Entity\Establishment;
use App\Entity\ProgramRequirement;
use App\Repository\EstablishmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-french-programs',
    description: 'Import French business schools programs from JSON file',
)]
class ImportFrenchProgramsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EstablishmentRepository $establishmentRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Importation des programmes des écoles françaises');

        // Chemin vers le fichier JSON
        $jsonFile = __DIR__ . '/../../french_schools_programs.json';

        if (!file_exists($jsonFile)) {
            $io->error('Le fichier french_schools_programs.json n\'existe pas.');
            return Command::FAILURE;
        }

        $jsonContent = file_get_contents($jsonFile);
        $data = json_decode($jsonContent, true);

        if (!$data || !isset($data['programs'])) {
            $io->error('Format de fichier JSON invalide.');
            return Command::FAILURE;
        }

        $programs = $data['programs'];
        $importedCount = 0;
        $skippedCount = 0;

        foreach ($programs as $programData) {
            try {
                // Vérifier si le programme existe déjà
                $existingProgram = $this->entityManager->getRepository(Program::class)
                    ->findOneBy(['name' => $programData['name']]);

                if ($existingProgram) {
                    $io->note(sprintf('Programme "%s" déjà existant, ignoré.', $programData['name']));
                    $skippedCount++;
                    continue;
                }

                // Trouver l'établissement
                $establishment = $this->findEstablishment($programData['establishment']['name']);
                if (!$establishment) {
                    $io->warning(sprintf(
                        'Établissement "%s" non trouvé pour le programme "%s"',
                        $programData['establishment']['name'],
                        $programData['name']
                    ));
                    continue;
                }

                // Créer le programme
                $program = $this->createProgram($programData, $establishment);
                $this->entityManager->persist($program);

                // Créer les exigences du programme
                if (isset($programData['detailedRequirements'])) {
                    $this->createProgramRequirements($program, $programData['detailedRequirements']);
                }

                $importedCount++;
                $io->text(sprintf('✓ Programme "%s" importé', $programData['name']));
            } catch (\Exception $e) {
                $io->error(sprintf(
                    'Erreur lors de l\'importation du programme "%s": %s',
                    $programData['name'],
                    $e->getMessage()
                ));
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf(
            'Importation terminée: %d programmes importés, %d ignorés',
            $importedCount,
            $skippedCount
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

        // Essayer de trouver par nom français
        $establishment = $this->establishmentRepository->findOneBy(['nameFr' => $establishmentName]);

        if ($establishment) {
            return $establishment;
        }

        // Recherche partielle
        $establishments = $this->establishmentRepository->createQueryBuilder('e')
            ->where('e.name LIKE :name OR e.nameFr LIKE :name')
            ->setParameter('name', '%' . $establishmentName . '%')
            ->getQuery()
            ->getResult();

        return $establishments[0] ?? null;
    }

    private function createProgram(array $data, Establishment $establishment): Program
    {
        $program = new Program();

        $program->setName($data['name']);
        $program->setNameFr($data['nameFr'] ?? $data['name']);
        $program->setDescription($data['description'] ?? null);
        $program->setDescriptionFr($data['descriptionFr'] ?? null);
        $program->setCurriculum($data['curriculum'] ?? null);
        $program->setCurriculumFr($data['curriculumFr'] ?? null);
        $program->setEstablishment($establishment);
        $program->setCountry($data['establishment']['country']);
        $program->setCity($data['establishment']['city']);
        $program->setDegree($data['degree'] ?? null);
        $program->setDuration($data['duration'] ?? null);
        $program->setLanguage($data['language'] ?? null);
        $program->setTuition($data['tuition'] ?? null);
        $program->setTuitionAmount($data['tuitionAmount'] ?? null);
        $program->setTuitionCurrency($data['tuitionCurrency'] ?? null);

        if (isset($data['startDate'])) {
            $program->setStartDate(new \DateTime($data['startDate']));
        }

        if (isset($data['applicationDeadline'])) {
            $program->setApplicationDeadline(new \DateTime($data['applicationDeadline']));
        }

        $program->setIntake($data['intake'] ?? null);
        $program->setScholarships($data['scholarships'] ?? false);
        $program->setFeatured($data['featured'] ?? false);
        $program->setAidvisorRecommended($data['aidvisorRecommended'] ?? false);
        $program->setEasyApply($data['easyApply'] ?? false);
        $program->setRanking($data['ranking'] ?? null);
        $program->setStudyType($data['studyType'] ?? null);
        $program->setUniversityType($data['universityType'] ?? null);
        $program->setSubject($data['subject'] ?? null);
        $program->setField($data['field'] ?? null);
        $program->setStudyLevel($data['studyLevel'] ?? null);
        $program->setRating($data['rating'] ?? null);
        $program->setReviews($data['reviews'] ?? null);
        $program->setIsActive($data['isActive'] ?? true);

        // Structured requirements
        if (isset($data['structuredRequirements'])) {
            $program->setStructuredRequirements($data['structuredRequirements']);
        }

        // Academic requirements
        if (isset($data['detailedRequirements'])) {
            $this->setAcademicRequirements($program, $data['detailedRequirements']);
        }

        $program->setCreatedAt(new \DateTime());
        $program->setUpdatedAt(new \DateTime());

        return $program;
    }

    private function setAcademicRequirements(Program $program, array $requirements): void
    {
        $academicQualifications = [];
        $gradeRequirements = [];
        $requiresAcademicQualification = false;
        $requiresGPA = false;
        $minimumGrade = null;
        $gradeSystem = null;
        $gpaScale = null;
        $gpaScore = null;

        foreach ($requirements as $req) {
            if ($req['type'] === 'academic_qualification') {
                $requiresAcademicQualification = true;
                $academicQualifications[] = $req['name'];
            } elseif ($req['type'] === 'grade' || $req['type'] === 'gpa') {
                $requiresGPA = true;
                $gradeRequirements[] = [
                    'type' => $req['type'],
                    'subtype' => $req['subtype'],
                    'minimumValue' => $req['minimumValue'],
                    'unit' => $req['unit'],
                    'system' => $req['system'],
                    'displayText' => $req['displayText']
                ];

                if ($req['minimumValue']) {
                    $minimumGrade = $req['minimumValue'];
                    $gradeSystem = $req['system'] ?? $req['subtype'];

                    if ($req['type'] === 'gpa') {
                        $gpaScale = $req['unit'];
                        $gpaScore = $req['minimumValue'];
                    }
                }
            }
        }

        $program->setAcademicQualifications($academicQualifications);
        $program->setGradeRequirements($gradeRequirements);
        $program->setRequiresAcademicQualification($requiresAcademicQualification);
        $program->setRequiresGPA($requiresGPA);
        $program->setMinimumGrade($minimumGrade);
        $program->setGradeSystem($gradeSystem);
        $program->setGpaScale($gpaScale);
        $program->setGpaScore($gpaScore);
    }

    private function createProgramRequirements(Program $program, array $requirements): void
    {
        foreach ($requirements as $reqData) {
            $requirement = new ProgramRequirement();
            $requirement->setProgram($program);
            $requirement->setType($reqData['type']);
            $requirement->setSubtype($reqData['subtype'] ?? null);
            $requirement->setName($reqData['name']);
            $requirement->setDescription($reqData['description']);
            $requirement->setMinimumValue($reqData['minimumValue'] ?? null);
            $requirement->setMaximumValue($reqData['maximumValue'] ?? null);
            $requirement->setUnit($reqData['unit'] ?? null);
            $requirement->setSystem($reqData['system'] ?? null);
            $requirement->setIsRequired($reqData['isRequired'] ?? true);
            // DisplayText is calculated automatically by getDisplayText() method
            // Percentage is calculated automatically by convertToPercentage() method
            $requirement->setMetadata($reqData['metadata'] ?? null);
            $requirement->setCreatedAt(new \DateTime());
            $requirement->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($requirement);
        }
    }
}
