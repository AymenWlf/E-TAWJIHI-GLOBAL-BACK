<?php

namespace App\Repository;

use App\Entity\ApplicationStep;
use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApplicationStep>
 */
class ApplicationStepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationStep::class);
    }

    public function findByApplication(Application $application): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.application = :application')
            ->setParameter('application', $application)
            ->orderBy('s.stepNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findCompletedSteps(Application $application): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.application = :application')
            ->andWhere('s.isCompleted = :completed')
            ->setParameter('application', $application)
            ->setParameter('completed', true)
            ->orderBy('s.stepNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPendingSteps(Application $application): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.application = :application')
            ->andWhere('s.isCompleted = :completed')
            ->setParameter('application', $application)
            ->setParameter('completed', false)
            ->orderBy('s.stepNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findStepByNumber(Application $application, int $stepNumber): ?ApplicationStep
    {
        return $this->createQueryBuilder('s')
            ->where('s.application = :application')
            ->andWhere('s.stepNumber = :stepNumber')
            ->setParameter('application', $application)
            ->setParameter('stepNumber', $stepNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getNextStep(Application $application): ?ApplicationStep
    {
        return $this->createQueryBuilder('s')
            ->where('s.application = :application')
            ->andWhere('s.isCompleted = :completed')
            ->setParameter('application', $application)
            ->setParameter('completed', false)
            ->orderBy('s.stepNumber', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getLastCompletedStep(Application $application): ?ApplicationStep
    {
        return $this->createQueryBuilder('s')
            ->where('s.application = :application')
            ->andWhere('s.isCompleted = :completed')
            ->setParameter('application', $application)
            ->setParameter('completed', true)
            ->orderBy('s.stepNumber', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function calculateProgress(Application $application): float
    {
        $totalSteps = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.application = :application')
            ->setParameter('application', $application)
            ->getQuery()
            ->getSingleScalarResult();

        if ($totalSteps === 0) {
            return 0.0;
        }

        $completedSteps = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.application = :application')
            ->andWhere('s.isCompleted = :completed')
            ->setParameter('application', $application)
            ->setParameter('completed', true)
            ->getQuery()
            ->getSingleScalarResult();

        return round(($completedSteps / $totalSteps) * 100, 2);
    }
}
