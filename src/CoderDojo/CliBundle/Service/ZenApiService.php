<?php

namespace CoderDojo\CliBundle\Service;

use CoderDojo\CliBundle\Service\ZenModel\Event;
use CoderDojo\WebsiteBundle\Command\CreateDojoCommand;
use GuzzleHttp\Client;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Polygon\Polygon;

class ZenApiService
{

    const DOJOS_BY_COUNTRY = <<<'GQL'
        query ($countryCode: String!) {
          dojoCollection: clubs(filterBy: {brand: CODERDOJO, countryCode: $countryCode}, first: 200) {
            dojos: nodes {
              uuid
              urlSlug: url
              name
              city: municipality
              lat: latitude
              lon: longitude
              email
              website
              twitter
              countryCode
              active
              discardedAt
            }
            totalCount
          }
        }
        GQL

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
     * Retrieve CoderDojo locations from RPF Clubs Api
     *
     * @param string $countryCode
     * @return hash
     */
    public function getDojosByCountry(string $countryCode) {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $body = json_encode({
                "query" => DOJOS_BY_COUNTRY,
                "variables" => {"countryCode" => $countryCode}
        });

        $response = $this->client->request('POST', 'https://clubs-api.raspberrypi.org/graphql', [
            'headers' => $headers,
            'body' => $body
        ]);

        $result = json_decode($response->getBody()->getContents());

        // Returns a hash data => { dojoCollection => { dojos [] } }
        $data = $result->data;
        return $data->dojoCollection;
    }

    /**
     * Retrieve CoderDojos in NL
     *
     * @return CreateDojoCommand[]
     */
    public function getNlDojos()
    {
        $dojos = getDojosByCountry("NL")
        $dojos = $dojos->dojos;

        return $this->dojoToCommand($dojos);
    }

    public function getBeDojos()
    {
        $dojos = getDojosByCountry("BE")
        $dojos = $dojos->dojos;

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
            $point = new Coordinate([$dojo->lat, $dojo->lon]);

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
        return Array.new();
    }

    /**
     * @param $dojos
     * @return array
     */
    private function dojoToCommand($dojos): array
    {
        $externalDojos = [];

        foreach ($dojos as $externalDojo) {
            $city = $externalDojo->city;
            $city = explode(",", $city);
            $city = array_pop($city);

            // Bug from Google API - Will be fixed by CDF Later
            if (preg_match('/Breezand/', $city)) {
                $city = 'Breezand';
            }

            $removed = false;

            // Stage 4 has been mapped to setting `active` to false.
            if (!$externalDojo->active) {
                $removed = true;
            }

            if (!isNull($externalDojo->discardedAt)) {
                $removed = true;
            }

            /**
             * Handle inconsistencies from Zen
             */
            $name = str_replace('CoderDojo ', '', $externalDojo->name);
            $name = str_replace('Coderdojo ', '', $name);
            $name = str_replace(' at ', ' @ ', $name);

            // verifiedAt and creatorEmail are not in the clubs-api (yet)
            $externalDojos[] = new CreateDojoCommand(
                $externalDojo->id,
                null,
                '',
                'https://zen.coderdojo.com/dojo/' . $externalDojo->urlSlug,
                $name,
                $city,
                $externalDojo->lat,
                $externalDojo->lon,
                $externalDojo->email,
                $externalDojo->website,
                $externalDojo->twitter,
                $externalDojo->countryCode,
                $removed
            );
        }

        return $externalDojos;
    }
}
