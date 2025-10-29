<?php

namespace App\Repository;

use App\Entity\UserFinalStepStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserFinalStepStatus>
 */
class UserFinalStepStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFinalStepStatus::class);
    }

    /**
     * Get user's final step statuses
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('ufss')
            ->leftJoin('ufss.finalStep', 'fs')
            ->addSelect('fs')
            ->where('ufss.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('fs.stepOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get user's status for a specific final step
     */
    public function findByUserAndFinalStep(int $userId, int $finalStepId): ?UserFinalStepStatus
    {
        return $this->createQueryBuilder('ufss')
            ->where('ufss.user = :userId')
            ->andWhere('ufss.finalStep = :finalStepId')
            ->setParameter('userId', $userId)
            ->setParameter('finalStepId', $finalStepId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
