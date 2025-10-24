<?php

namespace App\Repository;

use App\Entity\ApplicationDocument;
use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApplicationDocument>
 */
class ApplicationDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationDocument::class);
    }

    public function findByApplication(Application $application): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.application = :application')
            ->setParameter('application', $application)
            ->orderBy('d.documentType', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByApplicationAndType(Application $application, string $documentType): ?ApplicationDocument
    {
        return $this->createQueryBuilder('d')
            ->where('d.application = :application')
            ->andWhere('d.documentType = :documentType')
            ->setParameter('application', $application)
            ->setParameter('documentType', $documentType)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPendingDocuments(): array
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.application', 'a')
            ->leftJoin('a.user', 'u')
            ->addSelect('a', 'u')
            ->where('d.status = :status')
            ->setParameter('status', ApplicationDocument::STATUS_PENDING)
            ->orderBy('d.uploadedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findApprovedDocuments(Application $application): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.application = :application')
            ->andWhere('d.status = :status')
            ->setParameter('application', $application)
            ->setParameter('status', ApplicationDocument::STATUS_APPROVED)
            ->getQuery()
            ->getResult();
    }

    public function findRejectedDocuments(Application $application): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.application = :application')
            ->andWhere('d.status = :status')
            ->setParameter('application', $application)
            ->setParameter('status', ApplicationDocument::STATUS_REJECTED)
            ->getQuery()
            ->getResult();
    }

    public function countDocumentsByStatus(Application $application): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.status, COUNT(d.id) as count')
            ->where('d.application = :application')
            ->setParameter('application', $application)
            ->groupBy('d.status')
            ->getQuery()
            ->getResult();
    }

    public function isDocumentTypeUploaded(Application $application, string $documentType): bool
    {
        $count = $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->where('d.application = :application')
            ->andWhere('d.documentType = :documentType')
            ->setParameter('application', $application)
            ->setParameter('documentType', $documentType)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function getRequiredDocumentsStatus(Application $application, array $requiredTypes): array
    {
        $status = [];

        foreach ($requiredTypes as $type) {
            $status[$type] = $this->isDocumentTypeUploaded($application, $type);
        }

        return $status;
    }
}
