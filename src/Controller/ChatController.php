<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Form\ChatType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{

    public function view(int $chatId)
    {
        
    }

    #[Route('/chat/create', name: 'chat_create')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $chat = new Chat();
        $form = $this->createForm(ChatType::class, $chat);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($chat);
            $em->flush();
            return $this->redirectToRoute('message_create', [
                'chat' => $chat->getId(),
            ]);
        }

        return $this->render('chat_list.html.twig', [
            'chats' => $em->getRepository(Chat::class)->findAll(),
            'form'     => $form->createView(),
        ]);
    }

    #[Route('/chat/edit/{chat}', name: 'chat_edit', requirements: ['chat' => '\d+'])]
    public function edit(Chat $chat, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(ChatType::class, $chat);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($chat);
            $em->flush();
            return $this->redirectToRoute('chat_create');
        }

        return $this->render('chat_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/chat/delete/{chat}', name: 'chat_delete', requirements: ['chat' => '\d+'])]
    public function delete(Chat $chat, EntityManagerInterface $em)
    {
        $em->remove($chat);
        $em->flush();
        return $this->redirectToRoute('chat_create');
    }
    
}
