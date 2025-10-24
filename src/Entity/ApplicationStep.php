<?php

namespace App\Entity;

use App\Repository\ApplicationStepRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationStepRepository::class)]
#[ORM\Table(name: 'application_steps')]
class ApplicationStep
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Application::class, inversedBy: 'steps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Application $application = null;

    #[ORM\Column]
    private ?int $stepNumber = null;

    #[ORM\Column(length: 100)]
    private ?string $stepName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stepTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isCompleted = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $completedAt = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $stepData = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $requiredDocuments = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $validationErrors = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): static
    {
        $this->application = $application;
        return $this;
    }

    public function getStepNumber(): ?int
    {
        return $this->stepNumber;
    }

    public function setStepNumber(int $stepNumber): static
    {
        $this->stepNumber = $stepNumber;
        return $this;
    }

    public function getStepName(): ?string
    {
        return $this->stepName;
    }

    public function setStepName(string $stepName): static
    {
        $this->stepName = $stepName;
        return $this;
    }

    public function getStepTitle(): ?string
    {
        return $this->stepTitle;
    }

    public function setStepTitle(?string $stepTitle): static
    {
        $this->stepTitle = $stepTitle;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): static
    {
        $this->isCompleted = $isCompleted;
        if ($isCompleted && !$this->completedAt) {
            $this->completedAt = new \DateTime();
        }
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeInterface $completedAt): static
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getStepData(): ?array
    {
        return $this->stepData;
    }

    public function setStepData(?array $stepData): static
    {
        $this->stepData = $stepData;
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

    public function getRequiredDocuments(): ?array
    {
        return $this->requiredDocuments;
    }

    public function setRequiredDocuments(?array $requiredDocuments): static
    {
        $this->requiredDocuments = $requiredDocuments;
        return $this;
    }

    public function getValidationErrors(): ?array
    {
        return $this->validationErrors;
    }

    public function setValidationErrors(?array $validationErrors): static
    {
        $this->validationErrors = $validationErrors;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function markAsCompleted(): static
    {
        $this->isCompleted = true;
        $this->completedAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function hasValidationErrors(): bool
    {
        return !empty($this->validationErrors);
    }
}
