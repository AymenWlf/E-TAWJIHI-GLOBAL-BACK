<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    /**
     * Find services by category
     */
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.category = :category')
            ->andWhere('s.isActive = :active')
            ->setParameter('category', $category)
            ->setParameter('active', true)
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find services available for specific countries
     */
    public function findByTargetCountries(array $countries): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.isActive = :active')
            ->setParameter('active', true);

        $orConditions = [];
        foreach ($countries as $index => $country) {
            $orConditions[] = "JSON_CONTAINS(s.targetCountries, :country{$index})";
            $qb->setParameter("country{$index}", json_encode($country));
        }

        if (!empty($orConditions)) {
            $qb->andWhere('(' . implode(' OR ', $orConditions) . ')');
        }

        return $qb->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all active services
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('s.category', 'ASC')
            ->addOrderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
