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

            $city = explode(",", $city);
            $city = array_pop($city);
            
            // Bug from Google API - Will be fixed by CDF Later
            if (preg_match('/Breezand/', $city)) {
                $city = 'Breezand';
            }

            $removed = false;

            if (4 === $externalDojo->stage) {
                $removed = true;
            }

            if (1 === $externalDojo->deleted) {
                $removed = true;
            }

            /**
             * Handle inconsistencies from Zen
             */
            $name = str_replace('CoderDojo ', '', $externalDojo->name);
            $name = str_replace('Coderdojo ', '', $name);
            $name = str_replace(' at ', ' @ ', $name);

            $externalDojos[] = new CreateDojoCommand(
                $externalDojo->id,
                $externalDojo->creatorEmail,
                'https://zen.coderdojo.com/dojo/'.$externalDojo->urlSlug,
                $name,
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

        $events = []
        foreach($dojoZenIds as $dojoId) {
          $response = $this->client->request('GET', 'https://zen.coderdojo.com/api/3.0/dojos/'.$dojoId.'/events', [
              'headers' => $headers,
          ]);
          $jsonResponse = json_decode($response->getBody()->getContents());
          $events = array_merge($events, $jsonResponse->results);
        }

        $externalEvents = [];

        foreach ($events as $externalEvent) {
            $externalEvents[] = new Event(
                $externalEvent->id,
                $externalEvent->dojoId,
                $externalEvent->name,
                new \DateTime($externalEvent->startTime),
                $externalEvent->status
            );
        }

        return $externalEvents;
    }
}
