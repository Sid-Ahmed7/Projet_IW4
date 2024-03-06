<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use ContainerOfhkJvc\getCompanyRepositoryService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Gedmo\Mapping\Annotation as Gedmo;


#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: User::class)]
    private Collection $users;

    // #[ORM\Column(type: 'uuid', unique: true)]
    // #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    // #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    // private $uuid;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $banner = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?int $phoneNumber = null;

    #[ORM\Column(nullable: true)]
    private ?int $taxNumber = null;

    #[ORM\Column]
    private ?int $siretNumber = null;

    #[ORM\Column(nullable: true)]
    private ?int $likes = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 50)]
    private ?string $state = null;

    
    #[ORM\Column(length: 255, nullable: true)]
    #[Gedmo\Slug(fields: ['name'])]

    private ?string $slug = null;



    #[ORM\OneToMany(mappedBy: 'Company', targetEntity: Devis::class)]
    private Collection $devis;

   

    #[ORM\Column]
    private ?bool $verified = null;

    #[ORM\OneToMany(mappedBy: 'Company', targetEntity: Negotiation::class)]
    private Collection $negotiations;

    #[ORM\Column(type: 'bigint',nullable: true)]
    private ?int $createdBy = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Reque::class)]
    private Collection $usr;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Reque::class)]
    private Collection $reques;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeCustomerID = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $categorie = null;

   


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->devis = new ArrayCollection();
        $this->negotiations = new ArrayCollection();
        $this->usr = new ArrayCollection();
        $this->reques = new ArrayCollection();
    }
    public function __toString(): string
    {
        return $this->name;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, user>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            if ($user->getCompany() === $this) {
                $user->setCompany(null);
            }
        }

        return $this;
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

    public function setAddress(?string $address): static
    {
        $this->address = $address;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?int $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getTaxNumber(): ?int
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(?int $taxNumber): static
    {
        $this->taxNumber = $taxNumber;

        return $this;
    }

    public function getSiretNumber(): ?int
    {
        return $this->siretNumber;
    }

    public function setSiretNumber(int $siretNumber): static
    {
        $this->siretNumber = $siretNumber;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): static
    {
        $this->likes = $likes;

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

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    // public function getUuid(): ?string
    // {
    //     return $this->uuid->toString();
    // }
    
    public function getSlug(): ?string
    {
        return $this->slug;
    }
 
    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

   
    /**
     * @return Collection<int, Devis>
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevis(Devis $devi): static
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

   

    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): static
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * @return Collection<int, Negotiation>
     */
    public function getNegotiations(): Collection
    {
        return $this->negotiations;
    }

    public function addNegotiation(Negotiation $negotiation): static
    {
        if (!$this->negotiations->contains($negotiation)) {
            $this->negotiations->add($negotiation);
            $negotiation->setCompany($this);
        }

        return $this;
    }

    public function removeNegotiation(Negotiation $negotiation): static
    {
        if ($this->negotiations->removeElement($negotiation)) {
            // set the owning side to null (unless already changed)
            if ($negotiation->getCompany() === $this) {
                $negotiation->setCompany(null);
            }
        }

        return $this;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection<int, Reque>
     */
    public function getUsr(): Collection
    {
        return $this->usr;
    }

    /**
     * @return Collection<int, Reque>
     */
    public function getReques(): Collection
    {
        return $this->reques;
    }

    // public function getUuid(): Uuid
    // {
    //     return $this->uuid;
    // }
    
   
//     public function findOneBySlug($slug): ?Company
// {
//     return $this->createQueryBuilder('c')
//         ->andWhere('c.slug = :slug')
//         ->setParameter('slug', $slug)
//         ->getQuery()
//         ->getOneOrNullResult();
// }

public function getStripeCustomerID(): ?string
{
    return $this->stripeCustomerID;
}

public function setStripeCustomerID(?string $stripeCustomerID): static
{
    $this->stripeCustomerID = $stripeCustomerID;

    return $this;
}

public function getCategorie(): ?string
{
    return $this->categorie;
}

public function setCategorie(?string $categorie): static
{
    $this->categorie = $categorie;

    return $this;
}
   
   
}
