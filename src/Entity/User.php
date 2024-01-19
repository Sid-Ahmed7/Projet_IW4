<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message: 'ce nom d utilisateur est déja utilisé veuillez en selectionner un autre')]
#[UniqueEntity(fields: ['username'], message: 'ce nom d utilisateur est déja utilisé veuillez en selectionner un autre')]



class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: 50, nullable: true)] 
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    private ?string $username = null;

    // #[ORM\Column(type: 'uuid', unique: true)]
    // #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    // #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    // private $uuid;
    

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $signupDate ;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Company $company = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Plan::class)]
    private Collection $plans;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?UserPlan $userPlan = null;

   
    #[ORM\OneToMany(mappedBy: 'users', targetEntity: Devis::class)]
    private Collection $devis;

    #[ORM\OneToMany(mappedBy: 'users', targetEntity: Requests::class)]
    private Collection $requests;

    #[ORM\OneToMany(mappedBy: 'senderID', targetEntity: Message::class)]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'users', targetEntity: Notification::class)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'users', targetEntity: ConversationUser::class)]
    private Collection $conversationUsers;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthdate = null;

    public function __construct()
    {
        $this->plans = new ArrayCollection();
        $this->devis = new ArrayCollection();
        $this->requests = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->conversationUsers = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    // public function getUuid(): ?string
    // {
    //     return $this->uuid->toString();
    // }

    

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getSignupDate(): ?\DateTimeInterface
    {
        return $this->signupDate;
    }

    public function setSignupDate(\DateTimeInterface $signupDate): self
    {
        $this->signupDate = $signupDate;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;

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
            // set the owning side to null (unless already changed)
            if ($plan->getAuthor() === $this) {
                $plan->setAuthor(null);
            }
        }

        return $this;
    }

    public function getUserPlan(): ?UserPlan
    {
        return $this->userPlan;
    }

    public function setUserPlan(?UserPlan $userPlan): static
    {
        $this->userPlan = $userPlan;

        return $this;
    }

    
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): static
    {
        if (!$this->devis->contains($devi)) {
            $this->devis->add($devi);
            $devi->setUsers($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getUsers() === $this) {
                $devi->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Requests>
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(Requests $request): static
    {
        if (!$this->requests->contains($request)) {
            $this->requests->add($request);
            $request->setUsers($this);
        }

        return $this;
    }

    public function removeRequest(Requests $request): static
    {
        if ($this->requests->removeElement($request)) {
            // set the owning side to null (unless already changed)
            if ($request->getUsers() === $this) {
                $request->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setSenderID($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSenderID() === $this) {
                $message->setSenderID(null);
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
            $notification->setUsers($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUsers() === $this) {
                $notification->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ConversationUser>
     */
    public function getConversationUsers(): Collection
    {
        return $this->conversationUsers;
    }

    public function addConversationUser(ConversationUser $conversationUser): static
    {
        if (!$this->conversationUsers->contains($conversationUser)) {
            $this->conversationUsers->add($conversationUser);
            $conversationUser->setUsers($this);
        }

        return $this;
    }

    public function removeConversationUser(ConversationUser $conversationUser): static
    {
        if ($this->conversationUsers->removeElement($conversationUser)) {
            // set the owning side to null (unless already changed)
            if ($conversationUser->getUsers() === $this) {
                $conversationUser->setUsers(null);
            }
        }

        return $this;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }
}
