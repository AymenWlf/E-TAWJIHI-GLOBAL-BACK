<?php

namespace App\Entity;

use App\Repository\ParameterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParameterRepository::class)]
#[ORM\Table(name: 'parameters')]
#[ORM\UniqueConstraint(name: 'uniq_param_category_code', columns: ['category', 'code'])]
class Parameter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $category;

    #[ORM\Column(length: 128)]
    private string $code;

    #[ORM\Column(length: 255)]
    private string $labelEn;

    #[ORM\Column(length: 255)]
    private string $labelFr;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $descriptionEn = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $descriptionFr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $scoreRange = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $meta = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $parentCode = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $sortOrder = 0;

    public function getId(): ?int
    {
        return $id = $this->id;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getLabelEn(): string
    {
        return $this->labelEn;
    }

    public function setLabelEn(string $labelEn): self
    {
        $this->labelEn = $labelEn;
        return $this;
    }

    public function getLabelFr(): string
    {
        return $this->labelFr;
    }

    public function setLabelFr(string $labelFr): self
    {
        $this->labelFr = $labelFr;
        return $this;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function setMeta(?array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    public function getParentCode(): ?string
    {
        return $this->parentCode;
    }

    public function setParentCode(?string $parentCode): self
    {
        $this->parentCode = $parentCode;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function getDescriptionEn(): ?string
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn(?string $descriptionEn): self
    {
        $this->descriptionEn = $descriptionEn;
        return $this;
    }

    public function getDescriptionFr(): ?string
    {
        return $this->descriptionFr;
    }

    public function setDescriptionFr(?string $descriptionFr): self
    {
        $this->descriptionFr = $descriptionFr;
        return $this;
    }

    public function getScoreRange(): ?string
    {
        return $this->scoreRange;
    }

    public function setScoreRange(?string $scoreRange): self
    {
        $this->scoreRange = $scoreRange;
        return $this;
    }
}
