<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Message::class);       
    }

    public function findMessagesPaginated(Chat|int $chat, int $limit = 10, int $offset = 0)
    {

    }
}