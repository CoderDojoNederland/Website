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
        $client = new Client();

        $dojoResponse = $this->getNetherlandsDojos($client);

        $dojos = json_decode($dojoResponse->getBody()->getContents());
        $dojos = $dojos->Netherlands;

        $dojoZenIds = [];

        foreach($dojos as $dojo) {
            $dojoZenIds[] = $dojo->id;
            $output->writeln('Name: '.$dojo->name.' ('.$dojo->id.')');
            $output->writeln('Lat: '.$dojo->geoPoint->lat);
            $output->writeln('Lon: '.$dojo->geoPoint->lon);
            $output->writeln('Twitter: '.$dojo->twitter);

            try{
                $city = $dojo->placeName;
            } catch (\Exception $e) {
                $city = $dojo->place;
            }

            $city = explode(" ", $city);
            $city = array_pop($city);

            $output->writeln('City: '.$city);

            $internalDojo = $this->getContainer()->get('doctrine')
                ->getManager()
                ->getRepository('CoderdojoWebsiteBundle:Dojo')
                ->findOneBy(['zenId'=>$dojo->id]);

            if (null === $internalDojo) {
                $internalDojo = $this->getContainer()->get('doctrine')
                    ->getManager()
                    ->getRepository('CoderdojoWebsiteBundle:Dojo')
                    ->findBy(['city'=>$city, 'zenId' => null]);
            } else {
                $internalDojo = [$internalDojo];
            }

            if (1 === count($internalDojo)) {
                $output->writeln('Updating internal');

                $internalDojo = $internalDojo[0];
                /** @var Dojo $internalDojo */
                $internalDojo->setZenId($dojo->id);
                $internalDojo->setZenCreatorEmail($dojo->creatorEmail);
                $internalDojo->setZenUrl($dojo->urlSlug);
                $internalDojo->setName($dojo->name);
                $internalDojo->setLat($dojo->geoPoint->lat);
                $internalDojo->setLon($dojo->geoPoint->lon);
                $internalDojo->setEmail($dojo->email);
                $internalDojo->setWebsite($dojo->website);
                $internalDojo->setTwitter($dojo->twitter);
            } else {
                $internalDojo = new Dojo(
                    $dojo->id,
                    $dojo->name,
                    $city,
                    $dojo->geoPoint->lat,
                    $dojo->geoPoint->lon,
                    $dojo->email,
                    $dojo->website,
                    $dojo->twitter
                );

                $internalDojo->setZenCreatorEmail($dojo->creatorEmail);
                $internalDojo->setZenUrl($dojo->urlSlug);

                $this->getContainer()->get('doctrine')
                    ->getManager()->persist($internalDojo);

                $output->writeln('Could no match internal, ask: '.$dojo->creatorEmail);
                $output->writeln('Creating internal');
            }
            $output->writeln('*************************');
        }

        $this->getContainer()->get('doctrine')
            ->getManager()->flush();

        /** @var Dojo[] $newDojos */
        $newDojos = $this->getContainer()->get('doctrine')
            ->getManager()
            ->getRepository('CoderdojoWebsiteBundle:Dojo')
            ->findAll();

        foreach($newDojos as $newDojo) {
            if (count($newDojo->getOwners()) === 0) {
                $output->writeln('Unclaimed dojo -> '.$newDojo->getName());
            }
        }

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
