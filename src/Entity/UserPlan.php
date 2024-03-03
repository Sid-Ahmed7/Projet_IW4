<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use App\Repository\UserPlanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPlanRepository::class)]
class UserPlan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $uuid;

    #[ORM\ManyToOne(inversedBy: 'userPlans')]
    private ?Plan $plan = null;

    #[ORM\ManyToOne(inversedBy: 'userPlans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $subscriptionID = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): static
    {
        $this->plan = $plan;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid->toString();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSubscriptionID(): ?string
    {
        return $this->subscriptionID;
    }

    public function setSubscriptionID(string $subscriptionID): static
    {
        $this->subscriptionID = $subscriptionID;

        return $this;
    }
}
