<?php

namespace CoderDojo\CliBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailAllUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mailer:users:accept-privacy')
            ->setDescription('Send accept privacy mail to all users')
            ->addOption('dry-run', 'd')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getContainer()->get('doctrine')->getRepository('CoderDojoWebsiteBundle:User');
        $users = $repository->findBy([], ['id' => 'ASC']);

        $bar = new ProgressBar($output);
        $bar->setFormat('%current% of %max%'.PHP_EOL.'[%bar%] %percent:3s%%'.PHP_EOL.'%message% '.PHP_EOL);
        $bar->setBarCharacter('<info>></info>');
        $bar->setProgressCharacter('<comment>|</comment>');
        $bar->setEmptyBarCharacter('<info>.</info>');
        $bar->setMessage('Getting started');
        $bar->setRedrawFrequency(10);
        $bar->start(count($users));

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

            if (!$input->getOption('dry-run')) {
                $this->getContainer()->get('mailer')->send($message);
            }

            $bar->advance();
        }

        $bar->finish();
    }
}
