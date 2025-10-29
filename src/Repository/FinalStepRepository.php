<?php

namespace App\Repository;

use App\Entity\FinalStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FinalStep>
 */
class FinalStepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FinalStep::class);
    }

    /**
     * Get all active final steps ordered by stepOrder field
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('fs')
            ->where('fs.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('fs.stepOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get final step by stepOrder
     */
    public function findByStepOrder(int $stepOrder): ?FinalStep
    {
        return $this->createQueryBuilder('fs')
            ->where('fs.stepOrder = :stepOrder')
            ->andWhere('fs.isActive = :active')
            ->setParameter('stepOrder', $stepOrder)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
