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
    private ?plan $plan = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlan(): ?plan
    {
        return $this->plan;
    }

    public function setPlan(?plan $plan): static
    {
        $this->plan = $plan;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid->toString();
    }
}
