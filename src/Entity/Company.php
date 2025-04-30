<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 20)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 20)]
    private ?string $siretNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $banner = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 50)]
    private ?string $state = null;

    #[ORM\Column(nullable: true)]
    private ?int $createdBy = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: User::class)]
    private Collection $hubusers;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Devis::class)]
    private Collection $devis;

    #[ORM\Column(length: 34, nullable: true)]
    private ?string $iban = null;

    #[ORM\Column(length: 11, nullable: true)]
    private ?string $bic = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $balance = '0.00';

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $stripeMetadata = [];

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Invoice::class)]
    private Collection $invoices;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Reque::class)]
    private Collection $reques;

    #[ORM\ManyToOne(inversedBy: 'companies')]
    private ?User $user = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->hubusers = new ArrayCollection();
        $this->devis = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->reques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getSiretNumber(): ?string
    {
        return $this->siretNumber;
    }

    public function setSiretNumber(string $siretNumber): static
    {
        $this->siretNumber = $siretNumber;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;
        return $this;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner): static
    {
        $this->banner = $banner;
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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?int $createdBy): static
    {
        $this->createdBy = $createdBy;
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

    /**
     * @return Collection<int, User>
     */
    public function getHubusers(): Collection
    {
        return $this->hubusers;
    }

    public function addHubuser(User $hubuser): static
    {
        if (!$this->hubusers->contains($hubuser)) {
            $this->hubusers->add($hubuser);
            $hubuser->setCompany($this);
        }
        return $this;
    }

    public function removeHubuser(User $hubuser): static
    {
        if ($this->hubusers->removeElement($hubuser)) {
            if ($hubuser->getCompany() === $this) {
                $hubuser->setCompany(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Devis>
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): static
    {
        if (!$this->devis->contains($devi)) {
            $this->devis->add($devi);
            $devi->setCompany($this);
        }
        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            if ($devi->getCompany() === $this) {
                $devi->setCompany(null);
            }
        }
        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(?string $iban): static
    {
        $this->iban = $iban;
        return $this;
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function setBic(?string $bic): static
    {
        $this->bic = $bic;
        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(?string $balance): static
    {
        $this->balance = $balance;
        return $this;
    }

    public function getStripeMetadata(): ?array
    {
        return $this->stripeMetadata;
    }

    public function setStripeMetadata(?array $stripeMetadata): static
    {
        $this->stripeMetadata = $stripeMetadata;
        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setCompany($this);
        }
        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            if ($invoice->getCompany() === $this) {
                $invoice->setCompany(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Reque>
     */
    public function getReques(): Collection
    {
        return $this->reques;
    }

    public function addReque(Reque $reque): static
    {
        if (!$this->reques->contains($reque)) {
            $this->reques->add($reque);
            $reque->setCompany($this);
        }
        return $this;
    }

    public function removeReque(Reque $reque): static
    {
        if ($this->reques->removeElement($reque)) {
            if ($reque->getCompany() === $this) {
                $reque->setCompany(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
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
}
