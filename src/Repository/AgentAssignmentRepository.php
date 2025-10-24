<?php

namespace App\Repository;

use App\Entity\AgentAssignment;
use App\Entity\User;
use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AgentAssignment>
 */
class AgentAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgentAssignment::class);
    }

    public function findByStudent(User $student): array
    {
        return $this->createQueryBuilder('aa')
            ->leftJoin('aa.agent', 'agent')
            ->leftJoin('aa.application', 'app')
            ->addSelect('agent', 'app')
            ->where('aa.student = :student')
            ->setParameter('student', $student)
            ->orderBy('aa.assignedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByAgent(User $agent): array
    {
        return $this->createQueryBuilder('aa')
            ->leftJoin('aa.student', 'student')
            ->leftJoin('aa.application', 'app')
            ->addSelect('student', 'app')
            ->where('aa.agent = :agent')
            ->setParameter('agent', $agent)
            ->orderBy('aa.assignedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveAssignmentsByAgent(User $agent): array
    {
        return $this->createQueryBuilder('aa')
            ->leftJoin('aa.student', 'student')
            ->leftJoin('aa.application', 'app')
            ->addSelect('student', 'app')
            ->where('aa.agent = :agent')
            ->andWhere('aa.status = :status')
            ->setParameter('agent', $agent)
            ->setParameter('status', AgentAssignment::STATUS_ACTIVE)
            ->orderBy('aa.assignedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveAssignmentByStudent(User $student): ?AgentAssignment
    {
        return $this->createQueryBuilder('aa')
            ->leftJoin('aa.agent', 'agent')
            ->leftJoin('aa.application', 'app')
            ->addSelect('agent', 'app')
            ->where('aa.student = :student')
            ->andWhere('aa.status = :status')
            ->setParameter('student', $student)
            ->setParameter('status', AgentAssignment::STATUS_ACTIVE)
            ->orderBy('aa.assignedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByAgentCode(string $agentCode): ?AgentAssignment
    {
        return $this->createQueryBuilder('aa')
            ->leftJoin('aa.agent', 'agent')
            ->leftJoin('aa.student', 'student')
            ->addSelect('agent', 'student')
            ->where('aa.agentCode = :agentCode')
            ->andWhere('aa.status = :status')
            ->setParameter('agentCode', $agentCode)
            ->setParameter('status', AgentAssignment::STATUS_ACTIVE)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByApplication(Application $application): ?AgentAssignment
    {
        return $this->createQueryBuilder('aa')
            ->leftJoin('aa.agent', 'agent')
            ->leftJoin('aa.student', 'student')
            ->addSelect('agent', 'student')
            ->where('aa.application = :application')
            ->setParameter('application', $application)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countActiveAssignmentsByAgent(User $agent): int
    {
        return $this->createQueryBuilder('aa')
            ->select('COUNT(aa.id)')
            ->where('aa.agent = :agent')
            ->andWhere('aa.status = :status')
            ->setParameter('agent', $agent)
            ->setParameter('status', AgentAssignment::STATUS_ACTIVE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAgentWorkload(): array
    {
        return $this->createQueryBuilder('aa')
            ->select('agent.id, agent.email, COUNT(aa.id) as assignmentCount')
            ->leftJoin('aa.agent', 'agent')
            ->where('aa.status = :status')
            ->setParameter('status', AgentAssignment::STATUS_ACTIVE)
            ->groupBy('agent.id')
            ->orderBy('assignmentCount', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBestAvailableAgent(): ?User
    {
        $result = $this->createQueryBuilder('aa')
            ->select('agent.id, COUNT(aa.id) as assignmentCount')
            ->leftJoin('aa.agent', 'agent')
            ->where('aa.status = :status')
            ->setParameter('status', AgentAssignment::STATUS_ACTIVE)
            ->groupBy('agent.id')
            ->orderBy('assignmentCount', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$result) {
            return null;
        }

        return $this->getEntityManager()
            ->getRepository(User::class)
            ->find($result['id']);
    }
}
