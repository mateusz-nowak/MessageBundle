<?php

namespace Matix\MessageBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    public function getQueueMessages($type)
    {
        return $this
            ->createQueryBuilder('m')
            ->where('m.type = :type AND m.sentAt IS NULL')
            ->setParameters(array('type' => $type))
            ->getQuery()
            ->execute();
    }
}
