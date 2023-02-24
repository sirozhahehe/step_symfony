<?php

namespace App\Service;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class MessageService
{
    public function __construct(
        private MessageRepository $messageRepository,
        private EntityManagerInterface $em,
        private Security $security,
    ) {}
    
    public function saveMessage(Message $message, ?int $replyToId = null)
    {
        $message->setSender($this->security->getUser());

        if ($replyToId) {
            $this->bindReply($message, $replyToId);
        }
        
        $this->em->persist($message);
        $this->em->flush();
    }

    private function bindReply(Message $message, int $replyToId)
    {
        if ($replyToMessage = $this->messageRepository->findOneBy(['id' => $replyToId])) {
            $message->setMessage($replyToMessage);
        }
    }

}
