<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'hubuser')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\SequenceGenerator(sequenceName: 'hubuser_id_seq', initialValue: 1, allocationSize: 1)]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    private ?string $lastname = null;

    #[ORM\Column(length: 50)]
    private ?string $username = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'hubusers')]
    private ?Company $company = null;

    #[ORM\OneToMany(mappedBy: 'hubuser', targetEntity: Devis::class)]
    private Collection $devis;

    #[ORM\OneToMany(mappedBy: 'hubuser', targetEntity: Invoice::class)]
    private Collection $invoices;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailVerificationToken = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Plan::class)]
    private Collection $plans;

    #[ORM\OneToMany(mappedBy: 'usr', targetEntity: Reque::class)]
    private Collection $reques;

    #[ORM\ManyToMany(targetEntity: Notification::class, mappedBy: 'users')]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Company::class)]
    private Collection $companies;

    #[ORM\Column(length: 20)]
    private string $accountType = 'personal';

    public const ACCOUNT_TYPE_PERSONAL = 'personal';
    public const ACCOUNT_TYPE_COMPANY = 'company';

    public function __construct()
    {
        $this->devis = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->roles = [];
        $this->createdAt = new \DateTimeImmutable();
        $this->plans = new ArrayCollection();
        $this->reques = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->companies = new ArrayCollection();
        // Ne définir accountType que si il n'est pas déjà défini
        if ($this->accountType === null) {
            $this->accountType = self::ACCOUNT_TYPE_PERSONAL;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;
        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
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

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
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
            $devi->setHubuser($this);
        }
        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            if ($devi->getHubuser() === $this) {
                $devi->setHubuser(null);
            }
        }
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
            $invoice->setHubuser($this);
        }
        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            if ($invoice->getHubuser() === $this) {
                $invoice->setHubuser(null);
            }
        }
        return $this;
    }

    public function getEmailVerificationToken(): ?string
    {
        return $this->emailVerificationToken;
    }

    public function setEmailVerificationToken(?string $emailVerificationToken): static
    {
        $this->emailVerificationToken = $emailVerificationToken;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    /**
     * @return Collection<int, Company>
     */
    public function getCompanies(): Collection
    {
        // Retourner toutes les entreprises liées à l'utilisateur
        if ($this->company && !$this->companies->contains($this->company)) {
            $this->companies->add($this->company);
        }
        return $this->companies;
    }

    public function addCompany(Company $company): static
    {
        if (!$this->getCompanies()->contains($company)) {
            $this->companies->add($company);
        }
        return $this;
    }

    public function removeCompany(Company $company): static
    {
        $this->companies->removeElement($company);
        return $this;
    }

    /**
     * @return Collection<int, Plan>
     */
    public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function addPlan(Plan $plan): static
    {
        if (!$this->plans->contains($plan)) {
            $this->plans->add($plan);
            $plan->setAuthor($this);
        }
        return $this;
    }

    public function removePlan(Plan $plan): static
    {
        if ($this->plans->removeElement($plan)) {
            if ($plan->getAuthor() === $this) {
                $plan->setAuthor(null);
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
            $reque->setUsr($this);
        }
        return $this;
    }

    public function removeReque(Reque $reque): static
    {
        if ($this->reques->removeElement($reque)) {
            if ($reque->getUsr() === $this) {
                $reque->setUsr(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->addUser($this);
        }
        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            $notification->removeUser($this);
        }
        return $this;
    }

    public function getAccountType(): string
    {
        return $this->accountType;
    }

    public function setAccountType(string $accountType): static
    {
        $this->accountType = $accountType;
        return $this;
    }

    public function isPersonalAccount(): bool
    {
        return $this->accountType === self::ACCOUNT_TYPE_PERSONAL;
    }

    public function isCompanyAccount(): bool
    {
        return $this->accountType === self::ACCOUNT_TYPE_COMPANY;
    }

    public static function getAccountTypeChoices(): array
    {
        return [
            'Compte Personnel (Freelance, Indépendant)' => self::ACCOUNT_TYPE_PERSONAL,
            'Compte Entreprise (Société, Organisation)' => self::ACCOUNT_TYPE_COMPANY,
        ];
    }
}