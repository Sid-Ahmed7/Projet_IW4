<?php

namespace App\Entity;

use App\Repository\NegotiationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NegotiationRepository::class)]
class Negotiation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Devis $devis = null;

    #[ORM\ManyToOne(inversedBy: 'negotiations')]
    private ?Company $company = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0')]
    private ?string $initialPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0', nullable: true)]
    private ?string $previewPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0', nullable: true)]
    private ?string $finalPrice = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateInterval $expirationTimeLeft = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): static
    {
        $this->devis = $devis;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getInitialPrice(): ?string
    {
        return $this->initialPrice;
    }

    public function setInitialPrice(string $initialPrice): static
    {
        $this->initialPrice = $initialPrice;

        return $this;
    }

    public function getPreviewPrice(): ?string
    {
        return $this->previewPrice;
    }

    public function setPreviewPrice(?string $previewPrice): static
    {
        $this->previewPrice = $previewPrice;

        return $this;
    }

    public function getFinalPrice(): ?string
    {
        return $this->finalPrice;
    }

    public function setFinalPrice(?string $finalPrice): static
    {
        $this->finalPrice = $finalPrice;

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

    public function getExpirationTimeLeft(): ?\DateInterval
    {
        return $this->expirationTimeLeft;
    }

    public function setExpirationTimeLeft(\DateInterval $expirationTimeLeft): static
    {
        $this->expirationTimeLeft = $expirationTimeLeft;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

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

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
