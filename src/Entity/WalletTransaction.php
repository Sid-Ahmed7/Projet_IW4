<?php

namespace App\Entity;

use App\Repository\WalletTransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WalletTransactionRepository::class)]
class WalletTransaction
{
    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';

    public const SOURCE_INVOICE_PAYMENT = 'invoice_payment';
    public const SOURCE_PAYOUT = 'payout';
    public const SOURCE_REFUND = 'refund';
    public const SOURCE_ADJUSTMENT = 'adjustment';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wallet $wallet = null;

    #[ORM\Column(length: 10)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(length: 50)]
    private ?string $source = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $metadata = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne]
    private ?Invoice $invoice = null;

    #[ORM\ManyToOne]
    private ?PayoutRequest $payoutRequest = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externalReference = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->metadata = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): static
    {
        $this->wallet = $wallet;

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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;

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

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): static
    {
        $this->metadata = $metadata;

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

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): static
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getPayoutRequest(): ?PayoutRequest
    {
        return $this->payoutRequest;
    }

    public function setPayoutRequest(?PayoutRequest $payoutRequest): static
    {
        $this->payoutRequest = $payoutRequest;

        return $this;
    }

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function setExternalReference(?string $externalReference): static
    {
        $this->externalReference = $externalReference;

        return $this;
    }

    // Méthodes utilitaires
    public function isCredit(): bool
    {
        return $this->type === self::TYPE_CREDIT;
    }

    public function isDebit(): bool
    {
        return $this->type === self::TYPE_DEBIT;
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_CREDIT => 'Crédit',
            self::TYPE_DEBIT => 'Débit',
            default => 'Inconnu'
        };
    }

    public function getSourceLabel(): string
    {
        return match ($this->source) {
            self::SOURCE_INVOICE_PAYMENT => 'Paiement facture',
            self::SOURCE_PAYOUT => 'Retrait',
            self::SOURCE_REFUND => 'Remboursement',
            self::SOURCE_ADJUSTMENT => 'Ajustement',
            default => 'Autre'
        };
    }

    public function getTypeIcon(): string
    {
        return match ($this->type) {
            self::TYPE_CREDIT => 'plus-circle',
            self::TYPE_DEBIT => 'minus-circle',
            default => 'circle'
        };
    }

    public function getTypeColor(): string
    {
        return match ($this->type) {
            self::TYPE_CREDIT => 'success',
            self::TYPE_DEBIT => 'danger',
            default => 'secondary'
        };
    }

    public function addMetadata(string $key, $value): static
    {
        if ($this->metadata === null) {
            $this->metadata = [];
        }
        $this->metadata[$key] = $value;

        return $this;
    }

    public function getMetadataValue(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }
}
