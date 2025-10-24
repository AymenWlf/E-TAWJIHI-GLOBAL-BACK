<?php

namespace App\Repository;

use App\Entity\Translation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Translation>
 */
class TranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Translation::class);
    }

    /**
     * Find translations by user
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find translations by user and status
     */
    public function findByUserAndStatus(User $user, string $status): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->andWhere('t.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', $status)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find translations by user and payment status
     */
    public function findByUserAndPaymentStatus(User $user, string $paymentStatus): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->andWhere('t.paymentStatus = :paymentStatus')
            ->setParameter('user', $user)
            ->setParameter('paymentStatus', $paymentStatus)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find pending translations for payment
     */
    public function findPendingForPayment(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->andWhere('t.paymentStatus = :paymentStatus')
            ->setParameter('user', $user)
            ->setParameter('paymentStatus', 'pending')
            ->orderBy('t.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calculate total amount for pending translations
     */
    public function getTotalPendingAmount(User $user): float
    {
        $result = $this->createQueryBuilder('t')
            ->select('SUM(t.totalPrice)')
            ->where('t.user = :user')
            ->andWhere('t.paymentStatus = :paymentStatus')
            ->setParameter('user', $user)
            ->setParameter('paymentStatus', 'pending')
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }
}
