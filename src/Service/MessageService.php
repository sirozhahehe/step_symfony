<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\Image;
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
    
    public function saveMessage(Message $message, ?int $replyToId = null, ?int $imageId = null)
    {
        $message->setSender($this->security->getUser());

        if ($replyToId) {
            $this->bindReply($message, $replyToId);
        }
        if ($imageId) {
            $this->bindImage($message, $imageId);
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

    private function bindImage(Message $message, int $imageId)
    {
        if ($image = $this->em->getRepository(Image::class)->findOneBy(['id' => $imageId])) {
            $message->setImage($image);
        }
    }

}
