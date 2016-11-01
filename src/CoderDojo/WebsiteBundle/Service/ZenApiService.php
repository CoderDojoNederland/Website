<?php

namespace CoderDojo\WebsiteBundle\Service;

use CoderDojo\WebsiteBundle\Service\ZenModel\Dojo;
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
     * @return Dojo[]
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

            $externalDojos[] = new Dojo(
                $externalDojo->id,
                $externalDojo->creatorEmail,
                $externalDojo->urlSlug,
                $externalDojo->name,
                $city,
                $externalDojo->geoPoint->lat,
                $externalDojo->geoPoint->lon,
                $externalDojo->email,
                $externalDojo->website,
                $externalDojo->twitter
            );
        }

        return $externalDojos;
    }

    /**
     * Retrieve CoderDojo Events from Zen Api
     */
    public function getEvents()
    {

    }
}