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



    #[Route('/', name: 'app_homepage')]
    public function index()
    {
        return $this->render('base.html.twig');
    }

}

