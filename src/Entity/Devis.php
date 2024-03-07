<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;


#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    // #[ORM\Column(type: 'uuid', unique: true)]
    // #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    // #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    // private $uuid;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    private ?Company $company = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    private ?User $users = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 50)]
    private ?string $state = null;

    #[ORM\Column]
    private ?bool $isNegotiable = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    #[Gedmo\Slug(fields: ['title',"id"])]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'devis', targetEntity: Invoice::class)]
    private Collection $invoices;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?int $operatorID = null;

    #[ORM\OneToMany(mappedBy: 'devis', targetEntity: DevisAsset::class)]
    private Collection $devisAssets;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->devisAssets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?company
    {
        return $this->company;
    }

    public function setCompany(?company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getUsers(): ?user
    {
        return $this->users;
    }

    public function setUsers(?user $users): static
    {
        $this->users = $users;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    // public function getUuid(): ?string
    // {
    //     return $this->uuid->toString();
    // }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function isIsNegotiable(): ?bool
    {
        return $this->isNegotiable;
    }

    public function setIsNegotiable(bool $isNegotiable): static
    {
        $this->isNegotiable = $isNegotiable;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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
            $invoice->setDevis($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getDevis() === $this) {
                $invoice->setDevis(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getOperatorID(): ?int
    {
        return $this->operatorID;
    }

    public function setOperatorID(?int $operatorID): static
    {
        $this->operatorID = $operatorID;

        return $this;
    }

    /**
     * @return Collection<int, DevisAsset>
     */
    public function getDevisAssets(): Collection
    {
        return $this->devisAssets;
    }

    public function addDevisAsset(DevisAsset $devisAsset): static
    {
        if (!$this->devisAssets->contains($devisAsset)) {
            $this->devisAssets->add($devisAsset);
            $devisAsset->setDevis($this);
        }

        return $this;
    }

    public function removeDevisAsset(DevisAsset $devisAsset): static
    {
        if ($this->devisAssets->removeElement($devisAsset)) {
            // set the owning side to null (unless already changed)
            if ($devisAsset->getDevis() === $this) {
                $devisAsset->setDevis(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->users;
    }
}
