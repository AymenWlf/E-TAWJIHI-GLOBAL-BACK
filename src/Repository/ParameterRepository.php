<?php

namespace App\Repository;

use App\Entity\Parameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Parameter>
 */
class ParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parameter::class);
    }

    /**
     * @return Parameter[]
     */
    public function findActiveByCategory(string $category): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :c')
            ->andWhere('p.isActive = :a')
            ->setParameter('c', $category)
            ->setParameter('a', true)
            ->orderBy('p.sortOrder', 'ASC')
            ->addOrderBy('p.labelEn', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Parameter[]
     */
    public function findAllActiveByCategories(array $categories): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category IN (:categories)')
            ->andWhere('p.isActive = :active')
            ->setParameter('categories', $categories)
            ->setParameter('active', true)
            ->orderBy('p.category', 'ASC')
            ->addOrderBy('p.sortOrder', 'ASC')
            ->addOrderBy('p.labelEn', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
