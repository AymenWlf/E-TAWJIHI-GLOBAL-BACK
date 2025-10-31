<?php

namespace App\Repository;

use App\Entity\DiagnosticQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DiagnosticQuestion>
 */
class DiagnosticQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiagnosticQuestion::class);
    }

    /**
     * Récupère toutes les questions actives triées par catégorie et ordre
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('q.category', 'ASC')
            ->addOrderBy('q.orderIndex', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les questions par catégorie
     */
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.category = :category')
            ->andWhere('q.isActive = :active')
            ->setParameter('category', $category)
            ->setParameter('active', true)
            ->orderBy('q.orderIndex', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les questions groupées par catégorie
     */
    public function findAllGroupedByCategory(): array
    {
        $questions = $this->findAllActive();
        $grouped = [];

        foreach ($questions as $question) {
            $category = $question->getCategory();
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $question;
        }

        return $grouped;
    }

    /**
     * Compte le nombre total de questions actives
     */
    public function countActive(): int
    {
        return $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->where('q.isActive = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult();
    }
}

