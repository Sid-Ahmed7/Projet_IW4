<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'likees')]
    private ?Company $comp = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    private ?User $hazer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComp(): ?Company
    {
        return $this->comp;
    }

    public function setComp(?Company $comp): static
    {
        $this->comp = $comp;

        return $this;
    }

    public function getHazer(): ?User
    {
        return $this->hazer;
    }

    public function setHazer(?User $hazer): static
    {
        $this->hazer = $hazer;

        return $this;
    }
}
