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
            ->setName('synchronise:dojo')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('coder_dojo.website_bundle.service.sync_service')->run($output);
        die();
        $dojoEvents = $this->getDojoEvents($client, $dojoZenIds);

        $dojoEvents = json_decode($dojoEvents->getBody()->getContents());

        foreach($dojoEvents as $event) {
            $output->writeln('handling event: '.$event->name);
            $output->writeln('Zen Dojo Id: '.$event->dojoId);

            /** @var DojoEvent $internalEvent */
            $internalEvent = $this->getContainer()->get('doctrine')
                ->getManager()
                ->getRepository('CoderdojoWebsiteBundle:DojoEvent')
                ->findOneBy(['zenId'=>$event->id]);

            /** @var Dojo $dojo */
            $dojo = $this->getContainer()->get('doctrine')
                ->getManager()
                ->getRepository('CoderdojoWebsiteBundle:Dojo')
                ->findOneBy(['zenId'=>$event->dojoId]);

            if (null === $dojo) {
                $output->writeln('No internal dojo found!');
                continue;
            }

            if (null === $internalEvent){
                $newEvent = new DojoEvent();
                $newEvent->setZenId($event->id);
                $newEvent->setName($event->name);
                $newEvent->setDojo($dojo);
                $newEvent->setType(DojoEvent::TYPE_ZEN);
                $newEvent->setDate(new \DateTime($event->dates[0]->startTime));
                $newEvent->setUrl($dojo->getZenUrl());
                $dojo->addEvent($newEvent);

                $this->getContainer()->get('doctrine')
                    ->getManager()
                    ->persist($newEvent);
            } else {
                $internalEvent->setName($event->name);
                $internalEvent->setDate(new \DateTime($event->dates[0]->startTime));
                $internalEvent->setUrl($dojo->getZenUrl());
            }
            $output->writeln('***********************');
        }

        $dojo = $this->getContainer()->get('doctrine')
            ->getManager()->flush();

        $output->writeln('Command result.');
    }

    /**
     * @param $client
     * @return ResponseInterface
     */
    private function getNetherlandsDojos(Client $client)
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $body = '{"query":{"verified": 1, "deleted": 0, "alpha2": "NL"}}';

        $response = $client->request('POST', 'https://zen.coderdojo.com/api/2.0/dojos/by-country', [
            'headers' => $headers,
            'body' => $body
        ]);

        return $response;
    }

    /**
     * @param $client
     * @return ResponseInterface
     */
    private function getDojoEvents(Client $client, $dojos)
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $dojoList = json_encode($dojos);

        $body = '{"query":{"dojo_id": {"in$":'.$dojoList.'}}}';

        $response = $client->request('POST', 'https://zen.coderdojo.com/api/2.0/events/search', [
            'headers' => $headers,
            'body' => $body
        ]);

        return $response;
    }

}
