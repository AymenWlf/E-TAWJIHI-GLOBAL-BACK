<?php

namespace App\Repository;

use App\Entity\Suggestion;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Suggestion>
 */
class SuggestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suggestion::class);
    }

    /**
     * Find suggestions by user
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.user = :user')
            ->setParameter('user', $user)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find public suggestions
     */
    public function findPublic(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.isPublic = :isPublic')
            ->setParameter('isPublic', true)
            ->orderBy('s.votes', 'DESC')
            ->addOrderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find suggestions by category
     */
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.category = :category')
            ->andWhere('s.isPublic = :isPublic')
            ->setParameter('category', $category)
            ->setParameter('isPublic', true)
            ->orderBy('s.votes', 'DESC')
            ->addOrderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find suggestions by status
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->setParameter('status', $status)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find most voted suggestions
     */
    public function findMostVoted(int $limit = 10): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.isPublic = :isPublic')
            ->setParameter('isPublic', true)
            ->orderBy('s.votes', 'DESC')
            ->addOrderBy('s.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find recent suggestions
     */
    public function findRecent(int $limit = 10): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.isPublic = :isPublic')
            ->setParameter('isPublic', true)
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Search suggestions
     */
    public function search(string $searchTerm): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.isPublic = :isPublic')
            ->andWhere('s.title LIKE :search OR s.description LIKE :search')
            ->setParameter('isPublic', true)
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('s.votes', 'DESC')
            ->addOrderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count suggestions by status
     */
    public function countByStatus(string $status): int
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count suggestions by user
     */
    public function countByUser(User $user): int
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get all categories
     */
    public function getCategories(): array
    {
        return $this->createQueryBuilder('s')
            ->select('DISTINCT s.category')
            ->where('s.isPublic = :isPublic')
            ->setParameter('isPublic', true)
            ->orderBy('s.category', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
