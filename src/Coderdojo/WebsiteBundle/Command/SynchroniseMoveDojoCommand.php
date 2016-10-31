<?php

namespace Coderdojo\WebsiteBundle\Command;

use Coderdojo\WebsiteBundle\Entity\Dojo;
use Coderdojo\WebsiteBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SynchroniseMoveDojoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('synchronise:move-dojo')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        /** @var User[] $users */
        $users = $em->getRepository('CoderdojoWebsiteBundle:User')->findAll();

        foreach($users as $user) {
            $output->writeln('handling '.$user->getName());

            $dojo = new Dojo(
                null,
                $user->getName(),
                $user->getCity(),
                $user->getLat(),
                $user->getLong(),
                $user->getEmail(),
                $user->getWebsite(),
                $user->getTwitter(),
                $user
            );

            $em->persist($dojo);
            $em->flush();

            $events = $user->getEvents();

            foreach ($events as $event) {
                $output->writeln("handling event: ".$event->getEventbriteId());
                $event->setDojo($dojo);
                $dojo->addEvent($event);
            }

            $em->flush();
        }

        $output->writeln('Done.');
    }

}
