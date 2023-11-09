<?php

namespace App\Entity;

use App\Repository\UserPreferencesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPreferencesRepository::class)]
class UserPreferences
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $users = null;

    #[ORM\Column(length: 10)]
    private ?string $langue = null;

    #[ORM\Column(length: 10)]
    private ?string $theme = null;

    #[ORM\Column]
    private ?bool $twoFactorAuth = null;

    #[ORM\Column]
    private ?bool $notifications = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): static
    {
        $this->users = $users;

        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): static
    {
        $this->langue = $langue;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function isTwoFactorAuth(): ?bool
    {
        return $this->twoFactorAuth;
    }

    public function setTwoFactorAuth(bool $twoFactorAuth): static
    {
        $this->twoFactorAuth = $twoFactorAuth;

        return $this;
    }

    public function isNotifications(): ?bool
    {
        return $this->notifications;
    }

    public function setNotifications(bool $notifications): static
    {
        $this->notifications = $notifications;

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
}
