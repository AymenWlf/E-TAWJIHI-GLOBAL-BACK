<?php

namespace App\Entity;

use App\Repository\ModificationRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ModificationRequestRepository::class)]
#[ORM\Table(name: 'modification_requests')]
class ModificationRequest
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['modification_request:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['modification_request:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Application::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['modification_request:read'])]
    private ?Application $application = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['modification_request:read'])]
    private ?string $reason = null;

    #[ORM\Column(length: 20)]
    #[Groups(['modification_request:read'])]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['modification_request:read'])]
    private ?string $adminResponse = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['modification_request:read'])]
    private ?User $admin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['modification_request:read'])]
    private ?\DateTimeInterface $modificationAllowedUntil = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['modification_request:read'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['modification_request:read'])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['modification_request:read'])]
    private ?\DateTimeInterface $respondedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->status = self::STATUS_PENDING;
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

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): static
    {
        $this->application = $application;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;
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

    public function getAdminResponse(): ?string
    {
        return $this->adminResponse;
    }

    public function setAdminResponse(?string $adminResponse): static
    {
        $this->adminResponse = $adminResponse;
        return $this;
    }

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function setAdmin(?User $admin): static
    {
        $this->admin = $admin;
        return $this;
    }

    public function getModificationAllowedUntil(): ?\DateTimeInterface
    {
        return $this->modificationAllowedUntil;
    }

    public function setModificationAllowedUntil(?\DateTimeInterface $modificationAllowedUntil): static
    {
        $this->modificationAllowedUntil = $modificationAllowedUntil;
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

    public function getRespondedAt(): ?\DateTimeInterface
    {
        return $this->respondedAt;
    }

    public function setRespondedAt(?\DateTimeInterface $respondedAt): static
    {
        $this->respondedAt = $respondedAt;
        return $this;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isModificationAllowed(): bool
    {
        if (!$this->isApproved()) {
            return false;
        }

        if (!$this->modificationAllowedUntil) {
            return false;
        }

        return new \DateTime() <= $this->modificationAllowedUntil;
    }
}
