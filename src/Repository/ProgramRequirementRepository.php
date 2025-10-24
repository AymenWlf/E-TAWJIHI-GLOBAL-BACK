<?php

namespace App\Repository;

use App\Entity\ProgramRequirement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProgramRequirement>
 */
class ProgramRequirementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgramRequirement::class);
    }

    /**
     * Find requirements by program
     */
    public function findByProgram(int $programId): array
    {
        return $this->createQueryBuilder('pr')
            ->andWhere('pr.program = :programId')
            ->andWhere('pr.isActive = :active')
            ->setParameter('programId', $programId)
            ->setParameter('active', true)
            ->orderBy('pr.type', 'ASC')
            ->addOrderBy('pr.isRequired', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find requirements by type
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('pr')
            ->andWhere('pr.type = :type')
            ->andWhere('pr.isActive = :active')
            ->setParameter('type', $type)
            ->setParameter('active', true)
            ->orderBy('pr.program', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find requirements by type and subtype
     */
    public function findByTypeAndSubtype(string $type, string $subtype): array
    {
        return $this->createQueryBuilder('pr')
            ->andWhere('pr.type = :type')
            ->andWhere('pr.subtype = :subtype')
            ->andWhere('pr.isActive = :active')
            ->setParameter('type', $type)
            ->setParameter('subtype', $subtype)
            ->setParameter('active', true)
            ->orderBy('pr.program', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find grade requirements for filtering
     */
    public function findGradeRequirementsForFiltering(float $userGradePercentage, string $gradeSystem): array
    {
        $qb = $this->createQueryBuilder('pr')
            ->select('pr.program')
            ->andWhere('pr.type = :type')
            ->andWhere('pr.isActive = :active')
            ->andWhere('pr.isRequired = :required')
            ->setParameter('type', 'grade')
            ->setParameter('active', true)
            ->setParameter('required', true);

        // Add grade system filter if specified
        if ($gradeSystem) {
            $qb->andWhere('pr.system = :system')
                ->setParameter('system', $gradeSystem);
        }

        // Add grade comparison based on unit
        $qb->andWhere('
            CASE pr.unit
                WHEN \'4.0\' THEN (pr.minimumValue / 4.0) * 100
                WHEN \'5.0\' THEN (pr.minimumValue / 5.0) * 100
                WHEN \'7.0\' THEN (pr.minimumValue / 7.0) * 100
                WHEN \'10.0\' THEN (pr.minimumValue / 10.0) * 100
                WHEN \'20.0\' THEN (pr.minimumValue / 20.0) * 100
                WHEN \'100.0\' THEN pr.minimumValue
                WHEN \'percentage\' THEN pr.minimumValue
                ELSE 0
            END <= :userGradePercentage
        ')
            ->setParameter('userGradePercentage', $userGradePercentage);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find GPA requirements for filtering
     */
    public function findGPARequirementsForFiltering(float $userGradePercentage, string $gpaScale): array
    {
        $qb = $this->createQueryBuilder('pr')
            ->select('pr.program')
            ->andWhere('pr.type = :type')
            ->andWhere('pr.isActive = :active')
            ->andWhere('pr.isRequired = :required')
            ->setParameter('type', 'gpa')
            ->setParameter('active', true)
            ->setParameter('required', true);

        // Add GPA scale filter if specified
        if ($gpaScale) {
            $qb->andWhere('pr.unit = :unit')
                ->setParameter('unit', $gpaScale);
        }

        // Add GPA comparison based on unit
        $qb->andWhere('
            CASE pr.unit
                WHEN \'4.0\' THEN (pr.minimumValue / 4.0) * 100
                WHEN \'5.0\' THEN (pr.minimumValue / 5.0) * 100
                WHEN \'7.0\' THEN (pr.minimumValue / 7.0) * 100
                WHEN \'10.0\' THEN (pr.minimumValue / 10.0) * 100
                WHEN \'20.0\' THEN (pr.minimumValue / 20.0) * 100
                WHEN \'100.0\' THEN pr.minimumValue
                WHEN \'percentage\' THEN pr.minimumValue
                ELSE 0
            END <= :userGradePercentage
        ')
            ->setParameter('userGradePercentage', $userGradePercentage);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find academic qualification requirements
     */
    public function findAcademicQualificationRequirements(array $qualificationTypes): array
    {
        $qb = $this->createQueryBuilder('pr')
            ->select('pr.program')
            ->andWhere('pr.type = :type')
            ->andWhere('pr.isActive = :active')
            ->andWhere('pr.isRequired = :required')
            ->setParameter('type', 'academic_qualification')
            ->setParameter('active', true)
            ->setParameter('required', true);

        if (!empty($qualificationTypes)) {
            $qb->andWhere('pr.subtype IN (:subtypes)')
                ->setParameter('subtypes', $qualificationTypes);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Get all requirement types
     */
    public function getAllRequirementTypes(): array
    {
        return $this->createQueryBuilder('pr')
            ->select('DISTINCT pr.type')
            ->andWhere('pr.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('pr.type', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * Get all requirement subtypes for a given type
     */
    public function getSubtypesByType(string $type): array
    {
        return $this->createQueryBuilder('pr')
            ->select('DISTINCT pr.subtype')
            ->andWhere('pr.type = :type')
            ->andWhere('pr.isActive = :active')
            ->andWhere('pr.subtype IS NOT NULL')
            ->setParameter('type', $type)
            ->setParameter('active', true)
            ->orderBy('pr.subtype', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }
}
