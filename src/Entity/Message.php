<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('messages')]
class Message
{

    #[ORM\Id()]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    #[ORM\Column(type: 'string')]
    private $text;

    #[ORM\Column(type: 'datetime')]
    private $sentAt;

    #[ORM\ManyToOne(targetEntity: Chat::class, inversedBy: 'messages')]
    private Chat $chat;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn('sender_id', referencedColumnName: 'id')]
    private ?User $sender = null;

    public function __construct(?string $text = null)
    {
        $this->text = $text;
        $this->sentAt = new \DateTime();
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getSentAt()
    {
        return $this->sentAt;
    }

	public function getChat(): Chat
    {
		return $this->chat;
	}
		
	public function setChat(Chat $chat): self
    {
		$this->chat = $chat;
		return $this;
	}

	public function getSender(): ?User {
		return $this->sender;
	}
	
	public function setSender(User $sender): self {
		$this->sender = $sender;
		return $this;
	}
}