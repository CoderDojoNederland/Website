<?php

namespace CoderDojo\CliBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailAllUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mailer:users:accept-privacy')
            ->setDescription('Send accept privacy mail to all users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getContainer()->get('doctrine')->getRepository('CoderDojoWebsiteBundle:User');
        $users = $repository->findAll();

        foreach($users as $user) {
            $message = \Swift_Message::newInstance()
                ->setSubject('Jouw account op coderdojo.nl')
                ->setFrom('contact@coderdojo.nl', 'CoderDojo Nederland')
                ->setReplyTo('contact@coderdojo.nl')
                ->setTo($user->getEmail())
                ->setBcc('chris+accept-privacy@coderdojo.nl')
                ->setContentType('text/html')
                ->setBody(
                    $this->getContainer()->get('templating')->render(
                        ':Dashboard:Email/accept-privacy.html.twig',
                        [
                            'user' => $user
                        ]
                    )
                );

            $this->getContainer()->get('mailer')->send($message);
        }
    }
}
