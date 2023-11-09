<?php

namespace App\Entity;

use App\Repository\ConversationUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationUserRepository::class)]
class ConversationUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'conversationUsers')]
    private ?User $users = null;

    #[ORM\Column]
    private ?bool $isArchived = null;

    #[ORM\Column(nullable: true)]
    private ?int $unreadCount = null;

    #[ORM\Column]
    private ?bool $muteNotification = null;

    #[ORM\Column(length: 50)]
    private ?string $state = null;

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

    public function isIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): static
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getUnreadCount(): ?int
    {
        return $this->unreadCount;
    }

    public function setUnreadCount(?int $unreadCount): static
    {
        $this->unreadCount = $unreadCount;

        return $this;
    }

    public function isMuteNotification(): ?bool
    {
        return $this->muteNotification;
    }

    public function setMuteNotification(bool $muteNotification): static
    {
        $this->muteNotification = $muteNotification;

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
}
