<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?User $senderID = null;

    #[ORM\Column(length: 50)]
    private ?string $messageType = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contentText = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $state = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?Image $Image = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?Conversation $conversation = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?Document $document = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSenderID(): ?User
    {
        return $this->senderID;
    }

    public function setSenderID(?User $senderID): static
    {
        $this->senderID = $senderID;

        return $this;
    }

    public function getMessageType(): ?string
    {
        return $this->messageType;
    }

    public function setMessageType(string $messageType): static
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function getContentText(): ?string
    {
        return $this->contentText;
    }

    public function setContentText(string $contentText): static
    {
        $this->contentText = $contentText;

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

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->Image;
    }

    public function setImage(?Image $Image): static
    {
        $this->Image = $Image;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): static
    {
        $this->document = $document;

        return $this;
    }
}
