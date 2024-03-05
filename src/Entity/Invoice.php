<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?Devis $devis = null;

    #[ORM\Column(length: 255)]
    private ?string $stripePaymentID = null;

    #[ORM\Column(length: 50)]
    private ?string $paymentType = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0', nullable: true)]
    private ?string $Vat = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paymentDetails = null;

    #[ORM\Column(length: 50)]
    private ?string $state = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    // #[Gedmo\Slug(fields: ['name',"id"])]


    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?int $HTPrice = null;

    #[ORM\Column(nullable: true)]
    private ?int $ttcprice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevis(): ?devis
    {
        return $this->devis;
    }

    public function setDevis(?devis $devis): static
    {
        $this->devis = $devis;

        return $this;
    }

    public function getStripePaymentID(): ?string
    {
        return $this->stripePaymentID;
    }

    public function setStripePaymentID(string $stripePaymentID): static
    {
        $this->stripePaymentID = $stripePaymentID;

        return $this;
    }

    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    public function setPaymentType(string $paymentType): static
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    public function getVat(): ?string
    {
        return $this->Vat;
    }

    public function setVat(?string $Vat): static
    {
        $this->Vat = $Vat;

        return $this;
    }

    public function getPaymentDetails(): ?string
    {
        return $this->paymentDetails;
    }

    public function setPaymentDetails(?string $paymentDetails): static
    {
        $this->paymentDetails = $paymentDetails;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getHTPrice(): ?int
    {
        return $this->HTPrice;
    }

    public function setHTPrice(int $HTPrice): static
    {
        $this->HTPrice = $HTPrice;

        return $this;
    }

    public function getTtcprice(): ?int
    {
        return $this->ttcprice;
    }

    public function setTtcprice(?int $ttcprice): static
    {
        $this->ttcprice = $ttcprice;

        return $this;
    }
}
