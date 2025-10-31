<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToOne(targetEntity: UserProfile::class, inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: true)]
    private ?UserProfile $userProfile = null;

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

    // China-specific fields
    #[ORM\Column(nullable: true)]
    #[Groups(['application:read'])]
    private ?bool $isChina = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['application:read'])]
    private ?bool $isFrance = null;

    // Passport fields for China
    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['application:read'])]
    private ?string $passportNumber = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['application:read'])]
    private ?string $passportIssueDate = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['application:read'])]
    private ?string $passportExpirationDate = null;

    // Religion field
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['application:read'])]
    private ?string $religion = null;

    // Family members (JSON for father and mother)
    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['application:read'])]
    private ?array $familyMembers = null;

    #[ORM\OneToMany(mappedBy: 'application', targetEntity: ApplicationDocument::class, cascade: ['persist', 'remove'])]
    private Collection $documents;

    #[ORM\OneToMany(mappedBy: 'application', targetEntity: ApplicationStep::class, cascade: ['persist', 'remove'])]
    private Collection $steps;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->status = 'draft';
        $this->currentStep = 1;
        $this->progressPercentage = '0.00';
        $this->applicationData = [];
        $this->documents = new ArrayCollection();
        $this->steps = new ArrayCollection();
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

    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile(?UserProfile $userProfile): static
    {
        $this->userProfile = $userProfile;
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

    /**
     * @return Collection<int, ApplicationDocument>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(ApplicationDocument $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setApplication($this);
        }
        return $this;
    }

    public function removeDocument(ApplicationDocument $document): static
    {
        if ($this->documents->removeElement($document)) {
            if ($document->getApplication() === $this) {
                $document->setApplication(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, ApplicationStep>
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(ApplicationStep $step): static
    {
        if (!$this->steps->contains($step)) {
            $this->steps->add($step);
            $step->setApplication($this);
        }
        return $this;
    }

    public function removeStep(ApplicationStep $step): static
    {
        if ($this->steps->removeElement($step)) {
            if ($step->getApplication() === $this) {
                $step->setApplication(null);
            }
        }
        return $this;
    }

    public function updateTimestamp(): static
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function canBeSubmitted(): bool
    {
        // Check if all required steps are completed
        // This is a simplified version - you might want to add more complex validation
        return $this->status === 'draft' && $this->currentStep >= 5;
    }

    // China-specific getters and setters
    public function getIsChina(): ?bool
    {
        return $this->isChina;
    }

    public function setIsChina(?bool $isChina): static
    {
        $this->isChina = $isChina;
        return $this;
    }

    public function getIsFrance(): ?bool
    {
        return $this->isFrance;
    }

    public function setIsFrance(?bool $isFrance): static
    {
        $this->isFrance = $isFrance;
        return $this;
    }

    public function getPassportNumber(): ?string
    {
        return $this->passportNumber;
    }

    public function setPassportNumber(?string $passportNumber): static
    {
        $this->passportNumber = $passportNumber;
        return $this;
    }

    public function getPassportIssueDate(): ?string
    {
        return $this->passportIssueDate;
    }

    public function setPassportIssueDate(?string $passportIssueDate): static
    {
        $this->passportIssueDate = $passportIssueDate;
        return $this;
    }

    public function getPassportExpirationDate(): ?string
    {
        return $this->passportExpirationDate;
    }

    public function setPassportExpirationDate(?string $passportExpirationDate): static
    {
        $this->passportExpirationDate = $passportExpirationDate;
        return $this;
    }

    public function getReligion(): ?string
    {
        return $this->religion;
    }

    public function setReligion(?string $religion): static
    {
        $this->religion = $religion;
        return $this;
    }

    public function getFamilyMembers(): ?array
    {
        return $this->familyMembers;
    }

    public function setFamilyMembers(?array $familyMembers): static
    {
        $this->familyMembers = $familyMembers;
        return $this;
    }
}
