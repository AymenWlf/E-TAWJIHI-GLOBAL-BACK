<?php

namespace App\Entity;

use App\Repository\PreferencesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PreferencesRepository::class)]
#[ORM\Table(name: 'preferences')]
class Preferences
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'preferences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $preferredDestinations = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $preferredStudyLevel = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $preferredDegree = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $preferredIntakes = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $preferredSubjects = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $preferredCurrency = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $annualBudget = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $scholarshipRequired = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getPreferredDestinations(): ?array
    {
        return $this->preferredDestinations;
    }

    public function setPreferredDestinations(?array $preferredDestinations): self
    {
        $this->preferredDestinations = $preferredDestinations;
        return $this;
    }

    public function getPreferredStudyLevel(): ?string
    {
        return $this->preferredStudyLevel;
    }

    public function setPreferredStudyLevel(?string $preferredStudyLevel): self
    {
        $this->preferredStudyLevel = $preferredStudyLevel;
        return $this;
    }

    public function getPreferredDegree(): ?string
    {
        return $this->preferredDegree;
    }

    public function setPreferredDegree(?string $preferredDegree): self
    {
        $this->preferredDegree = $preferredDegree;
        return $this;
    }

    public function getPreferredIntakes(): ?array
    {
        return $this->preferredIntakes;
    }

    public function setPreferredIntakes(?array $preferredIntakes): self
    {
        $this->preferredIntakes = $preferredIntakes;
        return $this;
    }

    public function getPreferredSubjects(): ?array
    {
        return $this->preferredSubjects;
    }

    public function setPreferredSubjects(?array $preferredSubjects): self
    {
        $this->preferredSubjects = $preferredSubjects;
        return $this;
    }

    public function getPreferredCurrency(): ?string
    {
        return $this->preferredCurrency;
    }

    public function setPreferredCurrency(?string $preferredCurrency): self
    {
        $this->preferredCurrency = $preferredCurrency;
        return $this;
    }

    public function getAnnualBudget(): ?array
    {
        return $this->annualBudget;
    }

    public function setAnnualBudget(?array $annualBudget): self
    {
        $this->annualBudget = $annualBudget;
        return $this;
    }

    public function isScholarshipRequired(): ?bool
    {
        return $this->scholarshipRequired;
    }

    public function setScholarshipRequired(?bool $scholarshipRequired): self
    {
        $this->scholarshipRequired = $scholarshipRequired;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getPreferredCountry(): ?string
    {
        if ($this->preferredDestinations && !empty($this->preferredDestinations)) {
            return $this->preferredDestinations[0];
        }
        return null;
    }
}
