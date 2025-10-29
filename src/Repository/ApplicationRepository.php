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

    /**
     * Find applications by user
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.program', 'p')
            ->leftJoin('p.establishment', 'e')
            ->addSelect('p', 'e')
            ->where('a.user = :user')
            ->setParameter('user', $user)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find active application for user and program
     */
    public function findActiveByUserAndProgram(User $user, Program $program): ?Application
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->andWhere('a.program = :program')
            ->andWhere('a.status IN (:statuses)')
            ->setParameter('user', $user)
            ->setParameter('program', $program)
            ->setParameter('statuses', ['draft', 'submitted', 'under_review', 'pre_admission', 'enrolled', 'final_offer', 'visa_application', 'enroll'])
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find application by ID and user
     */
    public function findByIdAndUser(int $id, User $user): ?Application
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.program', 'p')
            ->leftJoin('p.establishment', 'e')
            ->addSelect('p', 'e')
            ->where('a.id = :id')
            ->andWhere('a.user = :user')
            ->setParameter('id', $id)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
