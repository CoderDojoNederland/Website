<?php

namespace CoderDojo\WebsiteBundle\Handler;

use CoderDojo\WebsiteBundle\Command\CreateDojoCommand;
use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Event\DojoCreatedEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use GuzzleHttp\Client;
use SimpleBus\Message\Recorder\RecordsMessages;

class CreateDojoHandler
{
    /**
     * @var RecordsMessages
     */
    private $eventRecorder;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * CreateDojoHandler constructor.
     * @param RecordsMessages $eventRecorder
     * @param Registry $doctrine
     */
    public function __construct(RecordsMessages $eventRecorder, Registry $doctrine)
    {
        $this->eventRecorder = $eventRecorder;
        $this->doctrine = $doctrine->getManager();
    }

    /**
     * @param CreateDojoCommand $command
     */
    public function handle(CreateDojoCommand $command)
    {
        $province = $this->retrieveProvince($command->getLat(), $command->getLon());

        $internalDojo = new Dojo(
            $command->getZenId(),
            $command->getName(),
            $command->getCity(),
            $command->getLat(),
            $command->getLon(),
            $command->getEmail(),
            $command->getWebsite(),
            $command->getTwitter(),
            $command->getCountry(),
            $province
        );

        $internalDojo->setZenCreatorEmail($command->getZenCreatorEmail());
        $internalDojo->setZenUrl($command->getZenUrl());

        $this->doctrine->persist($internalDojo);
        $this->doctrine->flush();

        $event = new DojoCreatedEvent(
            $command->getZenId(),
            $command->getZenCreatorEmail(),
            $command->getZenUrl(),
            $command->getName(),
            $command->getCity(),
            $command->getLat(),
            $command->getLon(),
            $command->getEmail(),
            $command->getWebsite(),
            $command->getTwitter(),
            $command->isRemoved()
        );

        $this->eventRecorder->record($event);
    }

    /**
     * @param float $lat
     * @param float $lon
     * @return string
     */
    private function retrieveProvince(float $lat, float $lon): ?string
    {
        $client = new Client();
        $response = $client->get(
            'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'&sensor=false&key=AIzaSyBy7V91eQVB-Uo70MTNxv-oErLPkgKtWJM'
        );
        $result = json_decode($response->getBody()->getContents(), true);
        $components = $result['results'][0]['address_components'];
        foreach($components as $component) {
            $levels = array_values($component['types']);
            foreach($levels as $key => $value) {
                if ($value === 'administrative_area_level_1') {
                    return $component['long_name'];
                }
            }
        }

        return null;
    }
}