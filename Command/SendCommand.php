<?php

namespace Matix\MessageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Buzz\Browser;
use Doctrine\ORM\EntityManager;
use Datetime;

class SendCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('message:send')
            ->setDescription('Send SMS messages in queue');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $config = $this->getContainer()->getParameter('sms');

        $this->sendMessageSMS($em, $config);
        $this->sendMessageVMS($em, $config);
    }

    /**
     * Send VMS Message
     */
    protected function sendMessageVMS(EntityManager $em, $config)
    {
        $browser = new Browser;

        $username = $config['username'];
        $password = md5($config['password']);

        // Send message VMS
        $smsMessages = $em->getRepository('MatixMessageBundle:Message')->getQueueMessages(2);

        foreach ($smsMessages as $message) {
            $responseUrl = $browser->get(sprintf('http://api.smsapi.pl/vms.do?username=%s&password=%s&to=%s&tts=%s',
                $username, $password, $message->getRecipient(), urlencode($message->getText())
            ));

            if (preg_match('/OK/', $responseUrl->getContent())) {
                $message->setSentAt(new Datetime());
                $em->merge($message);
                $em->flush();
            } else {
                echo sprintf('Wiadomość VMS [%d] nie została wysłana. Zostanie wysłana poźniej.' . PHP_EOL, $message->getId());
            }
        }
    }

    /**
     * Send SMS Message
     */
    protected function sendMessageSMS(EntityManager $em, $config)
    {
        $browser = new Browser;

        $username = $config['username'];
        $password = md5($config['password']);

        // Send message SMS
        $smsMessages = $em->getRepository('MatixMessageBundle:Message')->getQueueMessages(1);

        foreach ($smsMessages as $message) {
            $responseUrl = $browser->get(sprintf('http://api.smsapi.pl/sms.do?username=%s&password=%s&to=%s&eco=1&message=%s',
                $username, $password, $message->getRecipient(), urlencode($message->getText())
            ));

            if (preg_match('/OK/', $responseUrl->getContent())) {
                $message->setSentAt(new Datetime());
                $em->merge($message);
                $em->flush();
            } else {
                echo sprintf('Wiadomość SMS [%d] nie została wysłana. Zostanie wysłana poźniej.' . PHP_EOL, $message->getId());
            }
        }
    }
}
