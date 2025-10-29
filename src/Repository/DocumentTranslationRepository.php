<?php

namespace App\Repository;

use App\Entity\DocumentTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentTranslation>
 */
class DocumentTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentTranslation::class);
    }

    /**
     * Find translations by original document
     */
    public function findByOriginalDocument(int $documentId): array
    {
        return $this->createQueryBuilder('dt')
            ->andWhere('dt.originalDocument = :documentId')
            ->setParameter('documentId', $documentId)
            ->orderBy('dt.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find translation by original document and target language
     */
    public function findByDocumentAndLanguage(int $documentId, string $targetLanguage): ?DocumentTranslation
    {
        return $this->createQueryBuilder('dt')
            ->andWhere('dt.originalDocument = :documentId')
            ->andWhere('dt.targetLanguage = :targetLanguage')
            ->setParameter('documentId', $documentId)
            ->setParameter('targetLanguage', $targetLanguage)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
