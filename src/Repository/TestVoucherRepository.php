<?php

namespace App\Repository;

use App\Entity\TestVoucher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TestVoucher>
 */
class TestVoucherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestVoucher::class);
    }

    /**
     * Find active test vouchers ordered by sort order
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('tv')
            ->where('tv.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('tv.sortOrder', 'ASC')
            ->addOrderBy('tv.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find test vouchers by category
     */
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('tv')
            ->where('tv.category = :category')
            ->andWhere('tv.isActive = :active')
            ->setParameter('category', $category)
            ->setParameter('active', true)
            ->orderBy('tv.sortOrder', 'ASC')
            ->addOrderBy('tv.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find test vouchers by status
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('tv')
            ->where('tv.status = :status')
            ->andWhere('tv.isActive = :active')
            ->setParameter('status', $status)
            ->setParameter('active', true)
            ->orderBy('tv.sortOrder', 'ASC')
            ->addOrderBy('tv.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
