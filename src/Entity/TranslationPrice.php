<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'translation_prices')]
class TranslationPrice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'from_language', type: 'string', length: 10)]
    private ?string $fromLanguage = null;

    #[ORM\Column(name: 'to_language', type: 'string', length: 10)]
    private ?string $toLanguage = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(type: 'string', length: 3)]
    private ?string $currency = 'MAD';

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
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

    public function getFromLanguage(): ?string
    {
        return $this->fromLanguage;
    }

    public function setFromLanguage(string $fromLanguage): self
    {
        $this->fromLanguage = $fromLanguage;
        return $this;
    }

    public function getToLanguage(): ?string
    {
        return $this->toLanguage;
    }

    public function setToLanguage(string $toLanguage): self
    {
        $this->toLanguage = $toLanguage;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price ? (float) $this->price : null;
    }

    public function setPrice(float $price): self
    {
        $this->price = (string) $price;
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

