<?php

namespace App\Entity;

use App\Repository\RequeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequeRepository::class)]
class Reque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $eventdate = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $operatorID = null;

    #[ORM\ManyToOne(inversedBy: 'reques')]
    private ?User $usr = null;

    #[ORM\ManyToOne(inversedBy: 'reques')]
    private ?Company $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $eventLocation = null;

    #[ORM\Column(length: 40)]
    private ?string $eventCountry = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $eventCity = null;

    #[ORM\Column]
    private ?int $eventCode = null;

    #[ORM\Column(length: 50)]
    private ?string $lastame = null;

    #[ORM\Column(length: 50)]
    private ?string $firstname = null;

    #[ORM\Column]
    private ?int $phoneNumber = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxBudget = null;

    #[ORM\Column(length: 20)]
    private ?string $state = null;

    #[ORM\Column(length: 255)]
    private ?string $mail = null;

    #[ORM\Column(length: 255)]
    private ?string $object = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $companie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventdate(): ?\DateTimeInterface
    {
        return $this->eventdate;
    }

    public function setEventdate(?\DateTimeInterface $eventdate): static
    {
        $this->eventdate = $eventdate;

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

    public function getOperatorID(): ?int
    {
        return $this->operatorID;
    }

    public function setOperatorID(?int $operatorID): static
    {
        $this->operatorID = $operatorID;

        return $this;
    }

    public function getUsr(): ?User
    {
        return $this->usr;
    }

    public function setUsr(?User $usr): static
    {
        $this->usr = $usr;

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

    public function getEventLocation(): ?string
    {
        return $this->eventLocation;
    }

    public function setEventLocation(?string $eventLocation): static
    {
        $this->eventLocation = $eventLocation;

        return $this;
    }

    public function getEventCountry(): ?string
    {
        return $this->eventCountry;
    }

    public function setEventCountry(string $eventCountry): static
    {
        $this->eventCountry = $eventCountry;

        return $this;
    }

    public function getEventCity(): ?string
    {
        return $this->eventCity;
    }

    public function setEventCity(?string $eventCity): static
    {
        $this->eventCity = $eventCity;

        return $this;
    }

    public function getEventCode(): ?int
    {
        return $this->eventCode;
    }

    public function setEventCode(int $eventCode): static
    {
        $this->eventCode = $eventCode;

        return $this;
    }

    public function getLastame(): ?string
    {
        return $this->lastame;
    }

    public function setLastame(string $lastame): static
    {
        $this->lastame = $lastame;

        return $this;
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

    public function getPhoneNumber(): ?int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(int $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getMaxBudget(): ?int
    {
        return $this->maxBudget;
    }

    public function setMaxBudget(?int $maxBudget): static
    {
        $this->maxBudget = $maxBudget;

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

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(string $object): static
    {
        $this->object = $object;

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

    public function getCompanie(): ?int
    {
        return $this->companie;
    }

    public function setCompanie(int $companie): static
    {
        $this->companie = $companie;

        return $this;
    }
}
