<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Chat::class);
    }

    public function findChatsWithUser(User $user)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->innerJoin('c.users', 'u')
            ->andWhere('u.id = :user')
            ->setParameter('user', $user)
        ;

        return $qb->getQuery()->getResult();
    }
}