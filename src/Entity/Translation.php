<?php

namespace App\Entity;

use App\Repository\TranslationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TranslationRepository::class)]
#[ORM\Table(name: 'translations')]
class Translation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $originalFilename = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $originalFilePath = null;

    #[ORM\Column(type: 'string', length: 10)]
    private ?string $originalLanguage = null;

    #[ORM\Column(type: 'string', length: 10)]
    private ?string $targetLanguage = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $documentType = null;

    #[ORM\Column(type: 'integer')]
    private ?int $numberOfPages = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $pricePerPage = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $totalPrice = null;

    #[ORM\Column(type: 'string', length: 10)]
    private ?string $currency = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $status = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $paymentStatus = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $translatedFilePath = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $translatedFilename = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deliveryDate = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->status = 'pending';
        $this->paymentStatus = 'pending';
        $this->currency = 'MAD';
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

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;
        return $this;
    }

    public function getOriginalFilePath(): ?string
    {
        return $this->originalFilePath;
    }

    public function setOriginalFilePath(string $originalFilePath): self
    {
        $this->originalFilePath = $originalFilePath;
        return $this;
    }

    public function getOriginalLanguage(): ?string
    {
        return $this->originalLanguage;
    }

    public function setOriginalLanguage(string $originalLanguage): self
    {
        $this->originalLanguage = $originalLanguage;
        return $this;
    }

    public function getTargetLanguage(): ?string
    {
        return $this->targetLanguage;
    }

    public function setTargetLanguage(string $targetLanguage): self
    {
        $this->targetLanguage = $targetLanguage;
        return $this;
    }

    public function getDocumentType(): ?string
    {
        return $this->documentType;
    }

    public function setDocumentType(string $documentType): self
    {
        $this->documentType = $documentType;
        return $this;
    }

    public function getNumberOfPages(): ?int
    {
        return $this->numberOfPages;
    }

    public function setNumberOfPages(int $numberOfPages): self
    {
        $this->numberOfPages = $numberOfPages;
        return $this;
    }

    public function getPricePerPage(): ?float
    {
        return $this->pricePerPage;
    }

    public function setPricePerPage(float $pricePerPage): self
    {
        $this->pricePerPage = $pricePerPage;
        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(string $paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getTranslatedFilePath(): ?string
    {
        return $this->translatedFilePath;
    }

    public function setTranslatedFilePath(?string $translatedFilePath): self
    {
        $this->translatedFilePath = $translatedFilePath;
        return $this;
    }

    public function getTranslatedFilename(): ?string
    {
        return $this->translatedFilename;
    }

    public function setTranslatedFilename(?string $translatedFilename): self
    {
        $this->translatedFilename = $translatedFilename;
        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeImmutable
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(?\DateTimeImmutable $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;
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
}
