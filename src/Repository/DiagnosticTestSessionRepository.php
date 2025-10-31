<?php

namespace App\Repository;

use App\Entity\DiagnosticTestSession;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DiagnosticTestSession>
 */
class DiagnosticTestSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiagnosticTestSession::class);
    }

    /**
     * Trouve la session active d'un utilisateur
     */
    public function findActiveSessionByUser(User $user): ?DiagnosticTestSession
    {
        return $this->createQueryBuilder('s')
            ->where('s.user = :user')
            ->andWhere('s.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'in_progress')
            ->orderBy('s.startedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve toutes les sessions complétées d'un utilisateur
     */
    public function findCompletedSessionsByUser(User $user): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.user = :user')
            ->andWhere('s.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'completed')
            ->orderBy('s.completedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve la dernière session complétée d'un utilisateur
     */
    public function findLatestCompletedByUser(User $user): ?DiagnosticTestSession
    {
        return $this->createQueryBuilder('s')
            ->where('s.user = :user')
            ->andWhere('s.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'completed')
            ->orderBy('s.completedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

