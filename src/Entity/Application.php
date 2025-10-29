<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
#[ORM\Table(name: 'applications')]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['application:read', 'application:list'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['application:read', 'application:list'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Program::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['application:read', 'application:list'])]
    private ?Program $program = null;

    #[ORM\Column(length: 50)]
    #[Groups(['application:read', 'application:list'])]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['application:read', 'application:list'])]
    private ?int $agentId = null;

    #[ORM\Column]
    #[Groups(['application:read', 'application:list'])]
    private ?int $currentStep = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Groups(['application:read', 'application:list'])]
    private ?string $progressPercentage = '0.00';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['application:read'])]
    private ?string $notes = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['application:read'])]
    private ?array $applicationData = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['application:read'])]
    private ?array $submittedData = null;

    #[ORM\Column]
    #[Groups(['application:read', 'application:list'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['application:read', 'application:list'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['application:read', 'application:list'])]
    private ?\DateTimeImmutable $submittedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->status = 'draft';
        $this->currentStep = 1;
        $this->progressPercentage = '0.00';
        $this->applicationData = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): static
    {
        $this->program = $program;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getAgentId(): ?int
    {
        return $this->agentId;
    }

    public function setAgentId(?int $agentId): static
    {
        $this->agentId = $agentId;
        return $this;
    }

    public function getCurrentStep(): ?int
    {
        return $this->currentStep;
    }

    public function setCurrentStep(int $currentStep): static
    {
        $this->currentStep = $currentStep;
        return $this;
    }

    public function getProgressPercentage(): ?string
    {
        return $this->progressPercentage;
    }

    public function setProgressPercentage(string $progressPercentage): static
    {
        $this->progressPercentage = $progressPercentage;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getApplicationData(): ?array
    {
        return $this->applicationData;
    }

    public function setApplicationData(?array $applicationData): static
    {
        $this->applicationData = $applicationData;
        return $this;
    }

    public function getSubmittedData(): ?array
    {
        return $this->submittedData;
    }

    public function setSubmittedData(?array $submittedData): static
    {
        $this->submittedData = $submittedData;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getSubmittedAt(): ?\DateTimeImmutable
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(?\DateTimeImmutable $submittedAt): static
    {
        $this->submittedAt = $submittedAt;
        return $this;
    }

    public function updateTimestamp(): static
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
