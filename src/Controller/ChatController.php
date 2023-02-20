<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Form\ChatType;
use App\Form\MessageType;
use App\Repository\ChatRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ChatController extends AbstractController
{

    #[Route('/chat/{chat}', name: 'chat_view', requirements: ['chat' => '\d+'], methods: ['GET'])]
    public function view(Chat $chat, MessageRepository $messageRepository, UserRepository $userRepository)
    {
        return $this->render('chat.html.twig', [
            'users'    => $userRepository->findAllExcept($this->getUser()),
            'messages' => array_reverse($messageRepository->findMessagesPaginated($chat)),
            'chat'     => $chat,
            'form'     => $this->createForm(MessageType::class)->createView(),
        ]);
    }

    #[IsGranted('ROLE_MANAGER')]
    #[Route('/chat/create', name: 'chat_create')]
    public function create(Request $request, EntityManagerInterface $em)
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

    #[Route('/chat/personal', name: 'app_chat_create_personal')]
    public function getOrCreatePersonalChat(
        Request $request, 
        EntityManagerInterface $em, 
        UserRepository $userRepository, 
        ChatRepository $chatRepository,  
    ) {
        $user = $userRepository->findOneBy(['id' => $request->query->get('userId')]);
        if (!$user) {
            throw $this->createNotFoundException();
        }
        $currentUser = $this->getUser();

        dump($chatRepository->findPersonalChat($user, $currentUser));die;

        $chat = new Chat();
        $chat
            ->addUserToChat($user)
            ->addUserToChat($currentUser)
        ;
        // $em->persist($chat);
        // $em->flush();

        dump($chat);die;

    }

    #[IsGranted('ROLE_MANAGER')]
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

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/chat/delete/{chat}', name: 'chat_delete', requirements: ['chat' => '\d+'])]
    public function delete(Chat $chat, EntityManagerInterface $em)
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
    )
    {
        $messages = $messageRepository->findMessagesPaginated(
            $chat,
            $request->query->get('limit', 10),
            $request->query->get('offset', 0),
        );

        return new JsonResponse(
            data: $serializer->serialize($messages, 'json', ['groups' => ['message']]),
            json: true
        );
    }
    
}
