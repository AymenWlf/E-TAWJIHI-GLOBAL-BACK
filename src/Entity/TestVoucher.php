<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'test_vouchers')]
class TestVoucher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $nameFr = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $vendor = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $vendorLogo = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $originalPrice = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $discountedPrice = null;

    #[ORM\Column(type: 'string', length: 3)]
    private ?string $currency = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $category = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $status = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'text')]
    private ?string $descriptionFr = null;

    #[ORM\Column(type: 'text')]
    private ?string $recognition = null;

    #[ORM\Column(type: 'text')]
    private ?string $recognitionFr = null;

    #[ORM\Column(type: 'json')]
    private ?array $features = null;

    #[ORM\Column(type: 'json')]
    private ?array $featuresFr = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $validity = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $validityFr = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $shareLink = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $buyLink = null;

    #[ORM\Column(type: 'string', length: 10)]
    private ?string $icon = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $color = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $sortOrder = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getNameFr(): ?string
    {
        return $this->nameFr;
    }

    public function setNameFr(string $nameFr): self
    {
        $this->nameFr = $nameFr;
        return $this;
    }

    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    public function setVendor(string $vendor): self
    {
        $this->vendor = $vendor;
        return $this;
    }

    public function getVendorLogo(): ?string
    {
        return $this->vendorLogo;
    }

    public function setVendorLogo(string $vendorLogo): self
    {
        $this->vendorLogo = $vendorLogo;
        return $this;
    }

    public function getOriginalPrice(): ?float
    {
        return $this->originalPrice;
    }

    public function setOriginalPrice(float $originalPrice): self
    {
        $this->originalPrice = $originalPrice;
        return $this;
    }

    public function getDiscountedPrice(): ?float
    {
        return $this->discountedPrice;
    }

    public function setDiscountedPrice(float $discountedPrice): self
    {
        $this->discountedPrice = $discountedPrice;
        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescriptionFr(): ?string
    {
        return $this->descriptionFr;
    }

    public function setDescriptionFr(string $descriptionFr): self
    {
        $this->descriptionFr = $descriptionFr;
        return $this;
    }

    public function getRecognition(): ?string
    {
        return $this->recognition;
    }

    public function setRecognition(string $recognition): self
    {
        $this->recognition = $recognition;
        return $this;
    }

    public function getRecognitionFr(): ?string
    {
        return $this->recognitionFr;
    }

    public function setRecognitionFr(string $recognitionFr): self
    {
        $this->recognitionFr = $recognitionFr;
        return $this;
    }

    public function getFeatures(): ?array
    {
        return $this->features;
    }

    public function setFeatures(array $features): self
    {
        $this->features = $features;
        return $this;
    }

    public function getFeaturesFr(): ?array
    {
        return $this->featuresFr;
    }

    public function setFeaturesFr(array $featuresFr): self
    {
        $this->featuresFr = $featuresFr;
        return $this;
    }

    public function getValidity(): ?string
    {
        return $this->validity;
    }

    public function setValidity(string $validity): self
    {
        $this->validity = $validity;
        return $this;
    }

    public function getValidityFr(): ?string
    {
        return $this->validityFr;
    }

    public function setValidityFr(string $validityFr): self
    {
        $this->validityFr = $validityFr;
        return $this;
    }

    public function getShareLink(): ?string
    {
        return $this->shareLink;
    }

    public function setShareLink(?string $shareLink): self
    {
        $this->shareLink = $shareLink;
        return $this;
    }

    public function getBuyLink(): ?string
    {
        return $this->buyLink;
    }

    public function setBuyLink(?string $buyLink): self
    {
        $this->buyLink = $buyLink;
        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
