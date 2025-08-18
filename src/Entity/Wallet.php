<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'wallet', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $balance = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $pendingBalance = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalEarnings = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalWithdrawn = '0.00';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'wallet', targetEntity: PayoutRequest::class, orphanRemoval: true)]
    private Collection $payoutRequests;

    #[ORM\OneToMany(mappedBy: 'wallet', targetEntity: WalletTransaction::class, orphanRemoval: true)]
    private Collection $transactions;

    public function __construct()
    {
        $this->payoutRequests = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): static
    {
        $this->balance = $balance;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getPendingBalance(): ?string
    {
        return $this->pendingBalance;
    }

    public function setPendingBalance(string $pendingBalance): static
    {
        $this->pendingBalance = $pendingBalance;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getTotalEarnings(): ?string
    {
        return $this->totalEarnings;
    }

    public function setTotalEarnings(string $totalEarnings): static
    {
        $this->totalEarnings = $totalEarnings;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getTotalWithdrawn(): ?string
    {
        return $this->totalWithdrawn;
    }

    public function setTotalWithdrawn(string $totalWithdrawn): static
    {
        $this->totalWithdrawn = $totalWithdrawn;
        $this->updatedAt = new \DateTimeImmutable();

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

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, PayoutRequest>
     */
    public function getPayoutRequests(): Collection
    {
        return $this->payoutRequests;
    }

    public function addPayoutRequest(PayoutRequest $payoutRequest): static
    {
        if (!$this->payoutRequests->contains($payoutRequest)) {
            $this->payoutRequests->add($payoutRequest);
            $payoutRequest->setWallet($this);
        }

        return $this;
    }

    public function removePayoutRequest(PayoutRequest $payoutRequest): static
    {
        if ($this->payoutRequests->removeElement($payoutRequest)) {
            // set the owning side to null (unless already changed)
            if ($payoutRequest->getWallet() === $this) {
                $payoutRequest->setWallet(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WalletTransaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(WalletTransaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setWallet($this);
        }

        return $this;
    }

    public function removeTransaction(WalletTransaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getWallet() === $this) {
                $transaction->setWallet(null);
            }
        }

        return $this;
    }

    // Méthodes utilitaires
    public function addToBalance(string $amount): static
    {
        $this->balance = number_format((float)$this->balance + (float)$amount, 2, '.', '');
        $this->totalEarnings = number_format((float)$this->totalEarnings + (float)$amount, 2, '.', '');
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function subtractFromBalance(string $amount): static
    {
        $this->balance = number_format((float)$this->balance - (float)$amount, 2, '.', '');
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function addToPendingBalance(string $amount): static
    {
        $this->pendingBalance = number_format((float)$this->pendingBalance + (float)$amount, 2, '.', '');
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function movePendingToBalance(string $amount): static
    {
        $this->pendingBalance = number_format((float)$this->pendingBalance - (float)$amount, 2, '.', '');
        $this->addToBalance($amount);

        return $this;
    }

    public function getAvailableBalance(): string
    {
        return $this->balance;
    }

    public function hasEnoughBalance(string $amount): bool
    {
        return (float)$this->balance >= (float)$amount;
    }
}
