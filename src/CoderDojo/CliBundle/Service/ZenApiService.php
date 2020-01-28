<?php

namespace CoderDojo\CliBundle\Service;

use CoderDojo\CliBundle\Service\ZenModel\Event;
use CoderDojo\WebsiteBundle\Command\CreateDojoCommand;
use GuzzleHttp\Client;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Polygon\Polygon;

class ZenApiService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * ZenApiService constructor.
     * @param string $kernelRootDir
     */
    public function __construct(string $kernelRootDir)
    {
        $this->client = new Client();
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * Retrieve CoderDojo locations from Zen Api
     *
     * @return CreateDojoCommand[]
     */
    public function getNlDojos()
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

        return $this->dojoToCommand($dojos);
    }

    public function getBeDojos()
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $body = '{"query":{"verified": 1, "deleted": 0, "alpha2": "BE"}}';

        $response = $this->client->request('POST', 'https://zen.coderdojo.com/api/2.0/dojos/by-country', [
            'headers' => $headers,
            'body' => $body
        ]);

        $dojos = json_decode($response->getBody()->getContents());
        $dojos = $dojos->Belgium;

        $kml = file_get_contents($this->kernelRootDir.'/kml/be-border.kml');
        $polygonArray = \geoPHP::load($kml, 'kml')->asArray();
        $polygon = new Polygon();
        
        foreach($polygonArray[0] as $polygonPoint)
        {
            $point = new Coordinate([$polygonPoint[1], $polygonPoint[0]]);
            $polygon->add($point);
        }

        $belgianDojos = [];

        foreach($dojos as $dojo)
        {
            $point = new Coordinate([$dojo->geoPoint->lat, $dojo->geoPoint->lon]);

            if ($polygon->pointInPolygon($point)) {
                $belgianDojos[] = $dojo;
            }
        }

        return $this->dojoToCommand($belgianDojos);
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

        $events = [];
        foreach($dojoZenIds as $dojoId) {
          $response = $this->client->request('GET', 'https://zen.coderdojo.com/api/3.0/dojos/'.$dojoId.'/events', [
              'headers' => $headers
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

    /**
     * @param $dojos
     * @return array
     */
    private function dojoToCommand($dojos): array
    {
        $externalDojos = [];

        foreach ($dojos as $externalDojo) {
            try {
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
                $externalDojo->verifiedAt,
                $externalDojo->creatorEmail ?? '',
                'https://zen.coderdojo.com/dojo/' . $externalDojo->urlSlug,
                $name,
                $city,
                $externalDojo->geoPoint->lat,
                $externalDojo->geoPoint->lon,
                $externalDojo->email,
                $externalDojo->website,
                $externalDojo->twitter,
                $externalDojo->country->alpha2,
                $removed
            );
        }

        return $externalDojos;
    }
}
