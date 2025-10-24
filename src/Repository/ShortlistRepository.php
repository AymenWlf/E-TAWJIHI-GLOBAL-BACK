<?php

namespace App\Repository;

use App\Entity\Shortlist;
use App\Entity\User;
use App\Entity\Program;
use App\Entity\Establishment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Shortlist>
 */
class ShortlistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shortlist::class);
    }

    public function findByUserAndProgram(User $user, Program $program): ?Shortlist
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.program = :program')
            ->setParameter('user', $user)
            ->setParameter('program', $program)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUserAndEstablishment(User $user, Establishment $establishment): ?Shortlist
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.establishment = :establishment')
            ->setParameter('user', $user)
            ->setParameter('establishment', $establishment)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
