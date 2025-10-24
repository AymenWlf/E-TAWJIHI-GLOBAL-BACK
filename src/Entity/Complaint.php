<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'complaints')]
class Complaint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $category = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $subject = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $priority = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $status = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $attachments = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $relatedService = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $relatedDocument = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $relatedTest = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $adminResponse = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $adminResponseDate = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $adminUserId = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(targetEntity: ComplaintMessage::class, mappedBy: 'complaint', cascade: ['persist', 'remove'])]
    private Collection $messages;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->messages = new ArrayCollection();
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
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

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
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

    public function getAttachments(): ?array
    {
        return $this->attachments;
    }

    public function setAttachments(?array $attachments): self
    {
        $this->attachments = $attachments;
        return $this;
    }

    public function getRelatedService(): ?string
    {
        return $this->relatedService;
    }

    public function setRelatedService(?string $relatedService): self
    {
        $this->relatedService = $relatedService;
        return $this;
    }

    public function getRelatedDocument(): ?string
    {
        return $this->relatedDocument;
    }

    public function setRelatedDocument(?string $relatedDocument): self
    {
        $this->relatedDocument = $relatedDocument;
        return $this;
    }

    public function getRelatedTest(): ?string
    {
        return $this->relatedTest;
    }

    public function setRelatedTest(?string $relatedTest): self
    {
        $this->relatedTest = $relatedTest;
        return $this;
    }

    public function getAdminResponse(): ?string
    {
        return $this->adminResponse;
    }

    public function setAdminResponse(?string $adminResponse): self
    {
        $this->adminResponse = $adminResponse;
        return $this;
    }

    public function getAdminResponseDate(): ?\DateTimeInterface
    {
        return $this->adminResponseDate;
    }

    public function setAdminResponseDate(?\DateTimeInterface $adminResponseDate): self
    {
        $this->adminResponseDate = $adminResponseDate;
        return $this;
    }

    public function getAdminUserId(): ?int
    {
        return $this->adminUserId;
    }

    public function setAdminUserId(?int $adminUserId): self
    {
        $this->adminUserId = $adminUserId;
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

    /**
     * @return Collection<int, ComplaintMessage>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(ComplaintMessage $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setComplaint($this);
        }

        return $this;
    }

    public function removeMessage(ComplaintMessage $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getComplaint() === $this) {
                $message->setComplaint(null);
            }
        }

        return $this;
    }
}
