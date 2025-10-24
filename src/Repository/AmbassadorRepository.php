<?php

namespace App\Repository;

use App\Entity\Ambassador;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ambassador>
 */
class AmbassadorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ambassador::class);
    }

    /**
     * Find ambassador by user
     */
    public function findByUser(User $user): ?Ambassador
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find active ambassadors
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.isActive = :isActive')
            ->andWhere('a.status = :status')
            ->setParameter('isActive', true)
            ->setParameter('status', 'active')
            ->orderBy('a.points', 'DESC')
            ->addOrderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find ambassadors by status
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', $status)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find ambassadors by university
     */
    public function findByUniversity(string $university): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.university LIKE :university')
            ->andWhere('a.isActive = :isActive')
            ->setParameter('university', '%' . $university . '%')
            ->setParameter('isActive', true)
            ->orderBy('a.points', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find top ambassadors by points
     */
    public function findTopByPoints(int $limit = 10): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('a.points', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find top ambassadors by referrals
     */
    public function findTopByReferrals(int $limit = 10): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('a.referrals', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Count ambassadors by status
     */
    public function countByStatus(string $status): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get all universities
     */
    public function getUniversities(): array
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT a.university')
            ->where('a.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('a.university', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all fields of study
     */
    public function getFieldsOfStudy(): array
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT a.fieldOfStudy')
            ->where('a.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('a.fieldOfStudy', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search ambassadors
     */
    public function search(string $searchTerm): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.isActive = :isActive')
            ->andWhere('a.university LIKE :search OR a.fieldOfStudy LIKE :search')
            ->setParameter('isActive', true)
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('a.points', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
