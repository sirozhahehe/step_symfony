<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * 1. на chat_list.html.twig сделать ссылку для каждого чата,
     *  которая будет вести на страницу редактирования чата
     * 
     * на странице редактирования чата отредактировать чат
     * 
     * редиректнуть на список чатов
     * 
     * 2. на chat_list.html.twig сделать ссылку для каждого чата,
     *  которая будет вести на страницу удаления чата
     * 
     *  после успешного удаления - редиректить на список чатов
     */



    /**
     * Сообщения
     *  - просмотреть
     *  - прикреплять файлы
     * 
     * Пользователи
     *  - регистрация
     *  - аутентификация
     *  - редактирование профиля
     *  - просмотреть профиля
     * 
     * Чат
     *  - создавать/удалять Сообщения
     *  - добавлять/удалять участников
     *  - редактировать чат
     */
    // Сообщения



    #[Route('/')]
    public function index()
    {
        return $this->render('base.html.twig');
    }


    #[Route('/another/{id}', requirements: ['id' => '\d+'])]
    public function anotherRoute(Request $request, $id)
    {
        $var = $request->query->get('get', 'default');
        return $this->render('another.html.twig', [
            'cars' => [
                'first' => 'Toyota',
                'second' => 'Nissan',
                'third' => 'Mazda',
            ],
        ]);
    }

    #[Route('/testRoute')]
    public function anotherJsonFunction(Request $request)
    {
        $type = $request->query->get('type');
        if (!in_array($type, ['html', 'json',])) {
            throw new BadRequestHttpException();
        }
        
        return $this->json($request->query->all());
    }

}

