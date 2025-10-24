<?php

namespace App\Service;

use App\Entity\Program;
use App\Entity\ProgramRequirement;
use App\Repository\ProgramRequirementRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProgramRequirementService
{
    private EntityManagerInterface $entityManager;
    private ProgramRequirementRepository $requirementRepository;

    public function __construct(EntityManagerInterface $entityManager, ProgramRequirementRepository $requirementRepository)
    {
        $this->entityManager = $entityManager;
        $this->requirementRepository = $requirementRepository;
    }

    /**
     * Migrate existing program data to dynamic requirements
     */
    public function migrateExistingProgramData(Program $program): void
    {
        // Deprecated: This service is no longer needed as we use structured requirements
        /*
        // Clear existing requirements
        foreach ($program->getRequirements() as $requirement) {
            $program->removeRequirement($requirement);
            $this->entityManager->remove($requirement);
        }

        // Migrate academic qualification requirements
        if ($program->isRequiresAcademicQualification()) {
            $this->addAcademicQualificationRequirement($program);
        }

        // Migrate grade requirements
        if ($program->getMinimumGrade() && $program->getGradeSystem()) {
            $this->addGradeRequirement($program);
        }

        // Migrate GPA requirements
        if ($program->isRequiresGPA() && $program->getGpaScore() && $program->getGpaScale()) {
            $this->addGPARequirement($program);
        }

        $this->entityManager->flush();
        */

        // This service is deprecated - use structured requirements instead
    }

    /**
     * Add academic qualification requirement
     */
    private function addAcademicQualificationRequirement(Program $program): void
    {
        $requirement = new ProgramRequirement();
        $requirement->setProgram($program);
        $requirement->setType('academic_qualification');
        $requirement->setSubtype('any');
        $requirement->setName('Academic Qualification Required');
        $requirement->setDescription('This program requires a valid academic qualification');
        $requirement->setIsRequired(true);
        $requirement->setIsActive(true);

        $program->addRequirement($requirement);
    }

    /**
     * Add grade requirement
     */
    private function addGradeRequirement(Program $program): void
    {
        $requirement = new ProgramRequirement();
        $requirement->setProgram($program);
        $requirement->setType('grade');
        $requirement->setSubtype($program->getGradeSystem());
        $requirement->setName('Minimum Grade Requirement');
        $requirement->setDescription("Minimum grade required: {$program->getMinimumGrade()}/{$program->getGradeSystem()}");
        $requirement->setMinimumValue($program->getMinimumGrade());
        $requirement->setSystem($program->getGradeSystem());
        $requirement->setIsRequired(true);
        $requirement->setIsActive(true);

        // Set unit based on grade system
        $unit = $this->getUnitFromGradeSystem($program->getGradeSystem());
        $requirement->setUnit($unit);

        $program->addRequirement($requirement);
    }

    /**
     * Add GPA requirement
     */
    private function addGPARequirement(Program $program): void
    {
        $requirement = new ProgramRequirement();
        $requirement->setProgram($program);
        $requirement->setType('gpa');
        $requirement->setSubtype($program->getGpaScale());
        $requirement->setName('Minimum GPA Requirement');
        $requirement->setDescription("Minimum GPA required: {$program->getGpaScore()}/{$program->getGpaScale()}");
        $requirement->setMinimumValue($program->getGpaScore());
        $requirement->setUnit($program->getGpaScale());
        $requirement->setIsRequired(true);
        $requirement->setIsActive(true);

        $program->addRequirement($requirement);
    }

    /**
     * Get unit from grade system
     */
    private function getUnitFromGradeSystem(string $gradeSystem): string
    {
        switch ($gradeSystem) {
            case 'CGPA_4':
            case 'GPA_4':
                return '4.0';
            case 'CGPA_5':
            case 'GPA_5':
                return '5.0';
            case 'CGPA_7':
            case 'GPA_7':
                return '7.0';
            case 'CGPA_10':
            case 'GPA_10':
                return '10.0';
            case 'CGPA_20':
            case 'GPA_20':
                return '20.0';
            case 'Percentage':
                return 'percentage';
            default:
                return '100.0';
        }
    }

    /**
     * Create a new requirement
     */
    public function createRequirement(
        Program $program,
        string $type,
        ?string $subtype = null,
        ?string $name = null,
        ?string $description = null,
        ?float $minimumValue = null,
        ?float $maximumValue = null,
        ?string $unit = null,
        ?string $system = null,
        bool $isRequired = true,
        ?array $metadata = null
    ): ProgramRequirement {
        $requirement = new ProgramRequirement();
        $requirement->setProgram($program);
        $requirement->setType($type);
        $requirement->setSubtype($subtype);
        $requirement->setName($name);
        $requirement->setDescription($description);
        $requirement->setMinimumValue($minimumValue ? (string) $minimumValue : null);
        $requirement->setMaximumValue($maximumValue ? (string) $maximumValue : null);
        $requirement->setUnit($unit);
        $requirement->setSystem($system);
        $requirement->setIsRequired($isRequired);
        $requirement->setIsActive(true);
        $requirement->setMetadata($metadata);

        $program->addRequirement($requirement);
        $this->entityManager->persist($requirement);

        return $requirement;
    }

    /**
     * Get standardized requirement types
     */
    public function getStandardizedRequirementTypes(): array
    {
        return [
            'academic_qualification' => [
                'name' => 'Academic Qualification',
                'subtypes' => [
                    'high_school' => 'High School Diploma',
                    'bachelor' => 'Bachelor\'s Degree',
                    'master' => 'Master\'s Degree',
                    'doctorate' => 'Doctorate',
                    'associate' => 'Associate Degree',
                    'certificate' => 'Professional Certificate',
                    'any' => 'Any Academic Qualification'
                ]
            ],
            'grade' => [
                'name' => 'Grade Requirement',
                'subtypes' => [
                    'cgpa4' => 'CGPA (4.0 Scale)',
                    'cgpa5' => 'CGPA (5.0 Scale)',
                    'cgpa7' => 'CGPA (7.0 Scale)',
                    'cgpa10' => 'CGPA (10.0 Scale)',
                    'cgpa20' => 'CGPA (20.0 Scale)',
                    'percentage' => 'Percentage (%)'
                ]
            ],
            'gpa' => [
                'name' => 'GPA Requirement',
                'subtypes' => [
                    '4.0' => 'GPA (4.0 Scale)',
                    '5.0' => 'GPA (5.0 Scale)',
                    '7.0' => 'GPA (7.0 Scale)',
                    '10.0' => 'GPA (10.0 Scale)',
                    '20.0' => 'GPA (20.0 Scale)',
                    '100.0' => 'GPA (100.0 Scale)'
                ]
            ],
            'language_test' => [
                'name' => 'Language Test',
                'subtypes' => [
                    'ielts' => 'IELTS',
                    'toefl' => 'TOEFL',
                    'duolingo' => 'Duolingo English Test',
                    'pte' => 'PTE Academic',
                    'cambridge' => 'Cambridge English',
                    'other' => 'Other Language Test'
                ]
            ],
            'standardized_test' => [
                'name' => 'Standardized Test',
                'subtypes' => [
                    'sat' => 'SAT',
                    'act' => 'ACT',
                    'gre' => 'GRE',
                    'gmat' => 'GMAT',
                    'lsat' => 'LSAT',
                    'mcat' => 'MCAT',
                    'other' => 'Other Standardized Test'
                ]
            ],
            'work_experience' => [
                'name' => 'Work Experience',
                'subtypes' => [
                    'professional' => 'Professional Experience',
                    'internship' => 'Internship Experience',
                    'volunteer' => 'Volunteer Experience',
                    'research' => 'Research Experience',
                    'any' => 'Any Work Experience'
                ]
            ],
            'portfolio' => [
                'name' => 'Portfolio',
                'subtypes' => [
                    'academic' => 'Academic Portfolio',
                    'creative' => 'Creative Portfolio',
                    'professional' => 'Professional Portfolio',
                    'research' => 'Research Portfolio'
                ]
            ],
            'interview' => [
                'name' => 'Interview',
                'subtypes' => [
                    'academic' => 'Academic Interview',
                    'professional' => 'Professional Interview',
                    'video' => 'Video Interview',
                    'in_person' => 'In-Person Interview'
                ]
            ],
            'essay' => [
                'name' => 'Essay/Personal Statement',
                'subtypes' => [
                    'personal_statement' => 'Personal Statement',
                    'statement_of_purpose' => 'Statement of Purpose',
                    'motivation_letter' => 'Motivation Letter',
                    'research_proposal' => 'Research Proposal'
                ]
            ],
            'recommendation' => [
                'name' => 'Letters of Recommendation',
                'subtypes' => [
                    'academic' => 'Academic References',
                    'professional' => 'Professional References',
                    'mixed' => 'Mixed References'
                ]
            ]
        ];
    }

    /**
     * Get programs filtered by requirements
     */
    public function getProgramsByRequirements(array $filters): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Program::class, 'p')
            ->leftJoin('p.requirements', 'pr')
            ->where('p.isActive = :active')
            ->andWhere('pr.isActive = :active')
            ->setParameter('active', true);

        // Filter by academic qualifications
        if (!empty($filters['academic_qualifications'])) {
            $qb->andWhere('pr.type = :academicType')
                ->andWhere('pr.subtype IN (:academicSubtypes)')
                ->setParameter('academicType', 'academic_qualification')
                ->setParameter('academicSubtypes', $filters['academic_qualifications']);
        }

        // Filter by grade requirements
        if (isset($filters['grade']) && isset($filters['grade_system'])) {
            $userGradePercentage = $this->convertGradeToPercentage($filters['grade'], $filters['grade_system']);
            if ($userGradePercentage !== null) {
                $qb->andWhere('
                    (pr.type = :gradeType AND 
                     CASE pr.unit
                         WHEN \'4.0\' THEN (pr.minimumValue / 4.0) * 100
                         WHEN \'5.0\' THEN (pr.minimumValue / 5.0) * 100
                         WHEN \'7.0\' THEN (pr.minimumValue / 7.0) * 100
                         WHEN \'10.0\' THEN (pr.minimumValue / 10.0) * 100
                         WHEN \'20.0\' THEN (pr.minimumValue / 20.0) * 100
                         WHEN \'100.0\' THEN pr.minimumValue
                         WHEN \'percentage\' THEN pr.minimumValue
                         ELSE 0
                     END <= :userGradePercentage)
                ')
                    ->setParameter('gradeType', 'grade')
                    ->setParameter('userGradePercentage', $userGradePercentage);
            }
        }

        // Filter by GPA requirements
        if (isset($filters['gpa']) && isset($filters['gpa_scale'])) {
            $userGpaPercentage = $this->convertGradeToPercentage($filters['gpa'], $filters['gpa_scale']);
            if ($userGpaPercentage !== null) {
                $qb->andWhere('
                    (pr.type = :gpaType AND 
                     CASE pr.unit
                         WHEN \'4.0\' THEN (pr.minimumValue / 4.0) * 100
                         WHEN \'5.0\' THEN (pr.minimumValue / 5.0) * 100
                         WHEN \'7.0\' THEN (pr.minimumValue / 7.0) * 100
                         WHEN \'10.0\' THEN (pr.minimumValue / 10.0) * 100
                         WHEN \'20.0\' THEN (pr.minimumValue / 20.0) * 100
                         WHEN \'100.0\' THEN pr.minimumValue
                         WHEN \'percentage\' THEN pr.minimumValue
                         ELSE 0
                     END <= :userGpaPercentage)
                ')
                    ->setParameter('gpaType', 'gpa')
                    ->setParameter('userGpaPercentage', $userGpaPercentage);
            }
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Convert grade to percentage for comparison
     */
    private function convertGradeToPercentage(float $grade, string $gradeSystem): ?float
    {
        switch ($gradeSystem) {
            case 'cgpa4':
            case 'gpa4':
                return ($grade / 4.0) * 100;
            case 'cgpa5':
            case 'gpa5':
                return ($grade / 5.0) * 100;
            case 'cgpa7':
            case 'gpa7':
                return ($grade / 7.0) * 100;
            case 'cgpa10':
            case 'gpa10':
                return ($grade / 10.0) * 100;
            case 'cgpa20':
            case 'gpa20':
                return ($grade / 20.0) * 100;
            case 'percentage':
                return $grade;
            default:
                return null;
        }
    }

    /**
     * Migrate all existing programs to use dynamic requirements
     */
    public function migrateAllPrograms(): int
    {
        $programs = $this->entityManager->getRepository(Program::class)->findAll();
        $migrated = 0;

        foreach ($programs as $program) {
            $this->migrateExistingProgramData($program);
            $migrated++;
        }

        $this->entityManager->flush();
        return $migrated;
    }
}
