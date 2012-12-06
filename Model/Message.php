<?php

namespace Matix\MessageBundle\Model;

use Matix\MessageBundle\Entity\Message as MessageEntity;
use Doctrine\ORM\EntityManager;

class Message
{
    /** var $em \Doctrine\ORM\EntityManager */
    private $em;

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function sendSMS($phone, $text)
    {
        $this->send($phone, $text, 1);
    }

    public function sendVMS($phone, $text)
    {
        $this->send($phone, $text, 2);
    }

    public function send($phone, $text, $type)
    {
        $em = $this->em;

        $message = new MessageEntity;
        $message->setType($type);

        $message->setText($text);
        $message->setRecipient($phone);

        $em->persist($message);
        $em->flush();
    }
}
