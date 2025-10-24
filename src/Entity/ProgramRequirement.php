<?php

namespace App\Entity;

use App\Repository\ProgramRequirementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgramRequirementRepository::class)]
#[ORM\Table(name: 'program_requirements')]
class ProgramRequirement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Program::class, inversedBy: 'requirements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Program $program = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null; // academic_qualification, grade, gpa, language_test, standardized_test, work_experience, etc.

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $subtype = null; // For academic_qualification: high_school, bachelor, master, etc.

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null; // Human readable name

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $minimumValue = null; // Minimum score/grade required

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $maximumValue = null; // Maximum score/grade (if applicable)

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $unit = null; // Scale or unit (4.0, 5.0, 10.0, 20.0, 100.0, percentage, etc.)

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $system = null; // System used (CGPA_4, GPA_5, Percentage, etc.)

    #[ORM\Column]
    private bool $isRequired = true; // Whether this requirement is mandatory

    #[ORM\Column]
    private bool $isActive = true; // Whether this requirement is currently active

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $metadata = null; // Additional metadata (test dates, validity periods, etc.)

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

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): static
    {
        $this->program = $program;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getSubtype(): ?string
    {
        return $this->subtype;
    }

    public function setSubtype(?string $subtype): static
    {
        $this->subtype = $subtype;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
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

    public function getMinimumValue(): ?string
    {
        return $this->minimumValue;
    }

    public function setMinimumValue(?string $minimumValue): static
    {
        $this->minimumValue = $minimumValue;
        return $this;
    }

    public function getMaximumValue(): ?string
    {
        return $this->maximumValue;
    }

    public function setMaximumValue(?string $maximumValue): static
    {
        $this->maximumValue = $maximumValue;
        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): static
    {
        $this->unit = $unit;
        return $this;
    }

    public function getSystem(): ?string
    {
        return $this->system;
    }

    public function setSystem(?string $system): static
    {
        $this->system = $system;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function setIsRequired(bool $isRequired): static
    {
        $this->isRequired = $isRequired;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): static
    {
        $this->metadata = $metadata;
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

    /**
     * Convert requirement value to percentage for comparison
     */
    public function convertToPercentage(): ?float
    {
        if (!$this->minimumValue || !$this->unit) {
            return null;
        }

        $value = (float) $this->minimumValue;
        $unit = $this->unit;

        switch ($unit) {
            case '4.0':
                return ($value / 4.0) * 100;
            case '5.0':
                return ($value / 5.0) * 100;
            case '7.0':
                return ($value / 7.0) * 100;
            case '10.0':
                return ($value / 10.0) * 100;
            case '20.0':
                return ($value / 20.0) * 100;
            case '100.0':
            case 'percentage':
                return $value;
            default:
                return null;
        }
    }

    /**
     * Get human readable requirement description
     */
    public function getDisplayText(): string
    {
        // For grade/GPA requirements, show concise format
        if (in_array($this->type, ['grade', 'gpa']) && $this->minimumValue && $this->system) {
            return "{$this->system}: {$this->minimumValue}";
        }

        // For language tests, show test name and score
        if ($this->type === 'language_test' && $this->minimumValue) {
            $testName = $this->name ?? $this->subtype ?? 'Language Test';
            return "{$testName}: {$this->minimumValue}";
        }

        // For standardized tests, show test name and score
        if ($this->type === 'standardized_test' && $this->minimumValue) {
            $testName = $this->name ?? $this->subtype ?? 'Standardized Test';
            return "{$testName}: {$this->minimumValue}";
        }

        // For academic qualifications, show just the name
        if ($this->type === 'academic_qualification') {
            return $this->name ?? $this->subtype ?? 'Academic Qualification';
        }

        // Default format for other types
        $text = $this->name ?? $this->type;
        if ($this->minimumValue && $this->unit) {
            $text .= ": {$this->minimumValue}";
        }

        return $text;
    }
}
