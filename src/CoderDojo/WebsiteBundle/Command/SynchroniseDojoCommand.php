<?php

namespace CoderDojo\WebsiteBundle\Command;

use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Entity\DojoEvent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SynchroniseDojoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('synchronise:zen')
            ->setDescription('pull dojos and events from zen')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('coder_dojo.website_bundle.service.sync_dojo_service')->run($output);
        $this->getContainer()->get('coder_dojo.website_bundle.service.sync_event_service')->run($output);
    }
}
