<?php

namespace App\Repository;

use App\Entity\FAQ;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FAQ>
 */
class FAQRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FAQ::class);
    }

    /**
     * Find active FAQs ordered by category and sort order
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('f.category', 'ASC')
            ->addOrderBy('f.sortOrder', 'ASC')
            ->addOrderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find FAQs by category
     */
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.category = :category')
            ->andWhere('f.isActive = :isActive')
            ->setParameter('category', $category)
            ->setParameter('isActive', true)
            ->orderBy('f.sortOrder', 'ASC')
            ->addOrderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find popular FAQs
     */
    public function findPopular(): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.isPopular = :isPopular')
            ->andWhere('f.isActive = :isActive')
            ->setParameter('isPopular', true)
            ->setParameter('isActive', true)
            ->orderBy('f.sortOrder', 'ASC')
            ->addOrderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all categories
     */
    public function getCategories(): array
    {
        return $this->createQueryBuilder('f')
            ->select('DISTINCT f.category')
            ->where('f.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('f.category', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search FAQs by question or answer
     */
    public function search(string $searchTerm): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.isActive = :isActive')
            ->andWhere('f.question LIKE :search OR f.answer LIKE :search OR f.questionFr LIKE :search OR f.answerFr LIKE :search')
            ->setParameter('isActive', true)
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('f.category', 'ASC')
            ->addOrderBy('f.sortOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
