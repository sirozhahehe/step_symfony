<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{

    #[Route('/message/create/{chat}', name: 'message_create', requirements: ['chat' => '\d+'])]
    public function create(Chat $chat, Request $request, EntityManagerInterface $em)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $message->setChat($chat);
            $em->persist($message);
            $em->flush();
            return $this->redirectToRoute('message_create', [
                'chat' => $chat->getId(),
            ]);
        }

        return $this->render('chat.html.twig', [
            'messages' => $chat->getMessages(),
            'form'     => $form->createView(),
        ]);
    }

    #[Route('/message/delete/{message}', name: 'message_delete', requirements: ['message' => '\d+'])]
    public function delete(Message $message, EntityManagerInterface $em)
    {
        $em->remove($message);
        $em->flush();
        
        return $this->redirectToRoute('message_create', [
            'chat' => $message->getChat()->getId(),
        ]);
    }

}