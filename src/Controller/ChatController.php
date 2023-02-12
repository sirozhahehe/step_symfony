<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Form\ChatType;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ChatController extends AbstractController
{

    #[Route('/chat/{chat}', name: 'chat_view', requirements: ['chat' => '\d+'], methods: ['GET'])]
    public function view(Chat $chat, MessageRepository $messageRepository): Response
    {
        return $this->render('chat.html.twig', [
            'messages' => array_reverse($messageRepository->findMessagesWithOffset($chat, 10)),
            'chat'     => $chat,
            'form'     => $this->createForm(MessageType::class)->createView(),
        ]);
    }

    #[Route('/chat/create', name: 'chat_create')]
    public function create(Request $request, EntityManagerInterface $em): RedirectResponse|Response
    {
        $chat = new Chat();
        $form = $this->createForm(ChatType::class, $chat);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($chat);
            $em->flush();
            return $this->redirectToRoute('chat_view', [
                'chat' => $chat->getId(),
            ]);
        }

        return $this->render('chat_list.html.twig', [
            'chats' => $em->getRepository(Chat::class)->findAll(),
            'form'     => $form->createView(),
        ]);
    }

    #[Route('/chat/edit/{chat}', name: 'chat_edit', requirements: ['chat' => '\d+'])]
    public function edit(Chat $chat, Request $request, EntityManagerInterface $em): RedirectResponse|Response
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
    public function delete(Chat $chat, EntityManagerInterface $em): RedirectResponse
    {
        $em->remove($chat);
        $em->flush();
        return $this->redirectToRoute('chat_create');
    }

    #[Route('/chat/{chat}/getMessages', name: 'app_get_messages')]
    public function getLastMessages(
        Chat $chat,
        MessageRepository $messageRepository,
        SerializerInterface $serializer,
        Request $request,
    ): JsonResponse
    {
        $messages = array_reverse($messageRepository->findMessagesWithOffset(
            $chat,
            $request->query->get('limit', 5),
            $request->query->get('offset', 0),
            $request->query->get('order', 'DESC'),
        ));
        return new JsonResponse(
            data: $serializer->serialize($messages, 'json', ['groups' => ['message']]),
            json: true
        );
    }
    
}
