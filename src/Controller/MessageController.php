<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use App\Form\MessageType;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{

    #[Route('/chat/{chat}', name: 'chat_send_message', requirements: ['chat' => '\d+'], methods: ['POST'])]
    public function create(Chat $chat, Request $request, MessageService $messageService)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $message->setChat($chat);
            $messageService->saveMessage(
                $message, 
                $form->get('replyTo')->getData(),
                $form->get('imageId')->getData(),
            );
        }

        return $this->json(
            data: $message,
            context: ['groups' => ['message']],
        );
    }

    #[Route('/message/delete/{message}', name: 'message_delete', requirements: ['message' => '\d+'])]
    public function delete(Message $message, EntityManagerInterface $em)
    {
        $em->remove($message);
        $em->flush();
        
        return $this->redirectToRoute('chat_view', [
            'chat' => $message->getChat()->getId(),
        ]);
    }

}