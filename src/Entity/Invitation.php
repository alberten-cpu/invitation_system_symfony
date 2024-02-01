<?php

namespace App\Entity;

use App\Repository\InvitationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
class Invitation
{
    const ACTION_ACCEPTED = 'accepted';
    const ACTION_DECLINED = 'declined';
    const ACTION_CANCELLED = 'cancelled';
    const ACTION_SEND = 'send';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "sender_id", referencedColumnName: "id", nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "receiver_id", referencedColumnName: "id", nullable: false)]
    private ?User $receiver = null;

    #[ORM\Column(type: "string", length: 20)]
    private ?string $action = null;

    #[ORM\Column(type: "boolean")]
    private ?bool $isRead = false;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $createdAt = null;

    // Add other fields as needed

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(): ?User
    {
        return $this->sender;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(): ?User
    {
        return $this->receiver;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        // Validate that the action is one of the allowed values
        if (!in_array($action, [self::ACTION_ACCEPTED, self::ACTION_DECLINED, self::ACTION_CANCELLED, self::ACTION_SEND])) {
            throw new \InvalidArgumentException('Invalid action value');
        }

        $this->action = $action;

        return $this;
    }

    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
