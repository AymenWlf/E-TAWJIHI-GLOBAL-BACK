<?php

namespace App\Repository;

use App\Entity\ModificationRequest;
use App\Entity\Application;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModificationRequest>
 */
class ModificationRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModificationRequest::class);
    }

    /**
     * Find active modification request for an application
     */
    public function findActiveByApplication(Application $application): ?ModificationRequest
    {
        return $this->createQueryBuilder('mr')
            ->where('mr.application = :application')
            ->andWhere('mr.status IN (:statuses)')
            ->setParameter('application', $application)
            ->setParameter('statuses', [ModificationRequest::STATUS_PENDING, ModificationRequest::STATUS_APPROVED])
            ->orderBy('mr.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find modification request by application and user
     */
    public function findByApplicationAndUser(Application $application, User $user): ?ModificationRequest
    {
        return $this->createQueryBuilder('mr')
            ->where('mr.application = :application')
            ->andWhere('mr.user = :user')
            ->setParameter('application', $application)
            ->setParameter('user', $user)
            ->orderBy('mr.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find all pending modification requests
     */
    public function findPending(): array
    {
        return $this->createQueryBuilder('mr')
            ->where('mr.status = :status')
            ->setParameter('status', ModificationRequest::STATUS_PENDING)
            ->orderBy('mr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find modification requests by user
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('mr')
            ->where('mr.user = :user')
            ->setParameter('user', $user)
            ->orderBy('mr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
