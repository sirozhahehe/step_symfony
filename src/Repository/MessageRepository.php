<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findMessagesWithOffset(Chat $chat, int $limit = 10, int $offset = 0, string $order = 'DESC')
    {
        $order = $order === 'DESC' ? 'DESC' : 'ASC';
        $qb = $this->createQueryBuilder('m');
        $qb
            ->andWhere('m.chat = :chat')
            ->setParameter('chat', $chat)

            ->setMaxResults($limit)
            ->setFirstResult($offset)

            ->orderBy('m.id', $order)
        ;

        return $qb->getQuery()->getResult();
    }
}