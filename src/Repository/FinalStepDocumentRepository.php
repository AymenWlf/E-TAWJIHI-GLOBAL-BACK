<?php

namespace App\Repository;

use App\Entity\FinalStepDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FinalStepDocument>
 */
class FinalStepDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FinalStepDocument::class);
    }

    /**
     * Get active documents for a final step
     */
    public function findActiveByFinalStep(int $finalStepId): array
    {
        return $this->createQueryBuilder('fsd')
            ->where('fsd.finalStep = :finalStepId')
            ->andWhere('fsd.isActive = :active')
            ->setParameter('finalStepId', $finalStepId)
            ->setParameter('active', true)
            ->orderBy('fsd.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
