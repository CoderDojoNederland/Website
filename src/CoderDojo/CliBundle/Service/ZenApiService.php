<?php

namespace CoderDojo\CliBundle\Service;

use CoderDojo\CliBundle\Service\ZenModel\Event;
use CoderDojo\WebsiteBundle\Command\CreateDojoCommand;
use GuzzleHttp\Client;

class ZenApiService
{
    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Retrieve CoderDojo locations from Zen Api
     *
     * @return CreateDojoCommand[]
     */
    public function getDojos()
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $body = '{"query":{"verified": 1, "deleted": 0, "alpha2": "NL"}}';

        $response = $this->client->request('POST', 'https://zen.coderdojo.com/api/2.0/dojos/by-country', [
            'headers' => $headers,
            'body' => $body
        ]);

        $dojos = json_decode($response->getBody()->getContents());
        $dojos = $dojos->Netherlands;

        $externalDojos = [];

        foreach ($dojos as $externalDojo) {
            try{
                $city = $externalDojo->placeName;
            } catch (\Exception $e) {
                $city = $externalDojo->place;
            }

            $city = explode(" ", $city);
            $city = array_pop($city);

            $removed = false;

            if (4 === $externalDojo->stage) {
                $removed = true;
            }

            if (1 === $externalDojo->deleted) {
                $removed = true;
            }

            $externalDojos[] = new CreateDojoCommand(
                $externalDojo->id,
                $externalDojo->creatorEmail,
                'https://zen.coderdojo.com/dojo/'.$externalDojo->urlSlug,
                $externalDojo->name,
                $city,
                $externalDojo->geoPoint->lat,
                $externalDojo->geoPoint->lon,
                $externalDojo->email,
                $externalDojo->website,
                $externalDojo->twitter,
                $removed
            );
        }

        return $externalDojos;
    }

    /**
     * Retrieve CoderDojo Events from Zen Api
     * @param array $dojoZenIds
     * @return Event[]
     */
    public function getEvents(array $dojoZenIds)
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $dojoList = json_encode($dojoZenIds);

        $body = '{"query":{"dojo_id": {"in$":'.$dojoList.'}}}';

        $response = $this->client->request('POST', 'https://zen.coderdojo.com/api/2.0/events/search', [
            'headers' => $headers,
            'body' => $body
        ]);

        $events = json_decode($response->getBody()->getContents());

        $externalEvents = [];

        foreach ($events as $externalEvent) {
            $externalEvents[] = new Event(
                $externalEvent->id,
                $externalEvent->dojoId,
                $externalEvent->name,
                new \DateTime($externalEvent->dates[0]->startTime),
                $externalEvent->status
            );
        }

        return $externalEvents;
    }
}