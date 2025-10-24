<?php

namespace App\Repository;

use App\Entity\Application;
use App\Entity\User;
use App\Entity\Program;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Application>
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.program', 'p')
            ->leftJoin('a.agent', 'agent')
            ->addSelect('p', 'agent')
            ->where('a.user = :user')
            ->setParameter('user', $user)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByAgent(User $agent): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.user', 'u')
            ->leftJoin('a.program', 'p')
            ->addSelect('u', 'p')
            ->where('a.agent = :agent')
            ->setParameter('agent', $agent)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByProgram(Program $program): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.user', 'u')
            ->leftJoin('a.agent', 'agent')
            ->addSelect('u', 'agent')
            ->where('a.program = :program')
            ->setParameter('program', $program)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.user', 'u')
            ->leftJoin('a.program', 'p')
            ->leftJoin('a.agent', 'agent')
            ->addSelect('u', 'p', 'agent')
            ->where('a.status = :status')
            ->setParameter('status', $status)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findDraftApplications(): array
    {
        return $this->findByStatus(Application::STATUS_DRAFT);
    }

    public function findSubmittedApplications(): array
    {
        return $this->findByStatus(Application::STATUS_SUBMITTED);
    }

    public function findPendingApplications(): array
    {
        return $this->findByStatus(Application::STATUS_UNDER_REVIEW);
    }

    public function findUserApplicationForProgram(User $user, Program $program): ?Application
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->andWhere('a.program = :program')
            ->setParameter('user', $user)
            ->setParameter('program', $program)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countApplicationsByStatus(): array
    {
        return $this->createQueryBuilder('a')
            ->select('a.status, COUNT(a.id) as count')
            ->groupBy('a.status')
            ->getQuery()
            ->getResult();
    }

    public function countApplicationsByAgent(User $agent): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.agent = :agent')
            ->andWhere('a.status != :cancelled')
            ->setParameter('agent', $agent)
            ->setParameter('cancelled', Application::STATUS_REJECTED)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
