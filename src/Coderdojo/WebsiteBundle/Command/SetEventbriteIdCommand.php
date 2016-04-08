<?php
namespace Coderdojo\WebsiteBundle\Command;

use Coderdojo\WebsiteBundle\Entity\DojoEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetEventbriteIdCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('coderdojo:set-eventbrite')
            ->setDescription('sets all eventbrite ids from urls')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        /** @var DojoEvent[] $events */
        $events = $em->getRepository('CoderdojoWebsiteBundle:DojoEvent')->findAll();

        foreach ($events as $dojoEvent) {
            $url = $dojoEvent->getUrl();
            $parts = explode('-', $url);

            $eid = array_pop($parts);
            $output->writeln('Setting id:' . $eid . 'for '.$dojoEvent->getDojo()->getName().'\r\n');

            $dojoEvent->setEventbriteId($eid);
        }

        $em->flush();

        $output->writeln('done');
    }
}