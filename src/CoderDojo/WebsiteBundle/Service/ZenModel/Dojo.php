<?php

namespace CoderDojo\WebsiteBundle\Service\ZenModel;

class Dojo
{
    /**
     * @var string
     */
    private $zenId;

    /**
     * @var string
     */
    private $zenCreatorEmail;

    /**
     * @var string
     */
    private $zenUrl;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $city;

    /**
     * @var float
     */
    private $lat;

    /**
     * @var float
     */
    private $lon;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $website;

    /**
     * @var string
     */
    private $twitter;

    /**
     * Dojo constructor.
     * @param string $zenId
     * @param string $zenCreatorEmail
     * @param string $zenUrl
     * @param string $name
     * @param string $city
     * @param float $lat
     * @param float $lon
     * @param string $email
     * @param string $website
     * @param string $twitter
     */
    public function __construct($zenId, $zenCreatorEmail, $zenUrl, $name, $city, $lat, $lon, $email, $website, $twitter)
    {
        $this->zenId = $zenId;
        $this->zenCreatorEmail = $zenCreatorEmail;
        $this->zenUrl = $zenUrl;
        $this->name = $name;
        $this->city = $city;
        $this->lat = $lat;
        $this->lon = $lon;
        $this->email = $email;
        $this->website = $website;
        $this->twitter = $twitter;
    }

    /**
     * @return string
     */
    public function getZenId()
    {
        return $this->zenId;
    }

    /**
     * @return string
     */
    public function getZenCreatorEmail()
    {
        return $this->zenCreatorEmail;
    }

    /**
     * @return string
     */
    public function getZenUrl()
    {
        return $this->zenUrl;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return float
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }
}