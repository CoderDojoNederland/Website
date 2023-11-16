<?php

namespace CoderDojo\WebsiteBundle\Command;

Use CoderDojo\WebsiteBundle\Entity\Dojo as Entity;

class CreateDojoCommand
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
     * @var bool
     */
    private $removed;

    /**
     * @var string
     */
    private $country;

    private $verifiedAt;

    /**
     * @param string $zenId
     * @param string $verifiedAt
     * @param string $zenCreatorEmail
     * @param string $zenUrl
     * @param string $name
     * @param string $city
     * @param float  $lat
     * @param float  $lon
     * @param string $email
     * @param string $website
     * @param string $twitter
     * @param        $country
     * @param bool   $removed
     */
    public function __construct($zenId, $verifiedAt, $zenCreatorEmail, $zenUrl, $name, $city, $lat, $lon, $email, $website, $twitter, $country, $removed)
    {
        $this->zenId = $zenId;
        $this->verifiedAt = $verifiedAt;
        $this->zenCreatorEmail = $zenCreatorEmail;
        $this->zenUrl = $zenUrl;
        $this->name = $name;
        $this->city = $city;
        $this->lat = round($lat, 5);
        $this->lon = round($lon, 5);
        $this->email = $email;
        if (null === $website) {
            $this->website = 'https://coderdojo.nl';
        } else {
            $this->website = $website;
        }
        $this->twitter = str_replace('@', '', $twitter);
        $this->twitter = str_replace('https://twitter.com/', '', $this->twitter);
        if ($this->twitter === ""){
            $this->twitter = "coderdojonl";
        }
        $this->removed = $removed;
        $this->country = $country;
    }

    public static function CreateFromEntity(Entity $entity)
    {
        $dojo = new self(
            $entity->getzenId(),
            $entity->getVerifiedAt() ? $entity->getVerifiedAt()->format(DATE_ATOM) : null,
            $entity->getzenCreatorEmail(),
            $entity->getzenUrl(),
            $entity->getname(),
            $entity->getcity(),
            $entity->getlat(),
            $entity->getlon(),
            $entity->getemail(),
            $entity->getwebsite(),
            $entity->gettwitter(),
            $entity->getCountry(),
            false
        );

        return $dojo;
    }

    /**
     * @return string
     */
    public function getZenId()
    {
        return $this->zenId;
    }

    /**
     * @return string|null
     */
    public function getVerifiedAt():? string
    {
        return $this->verifiedAt;
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
        return empty($this->city) ? null : $this->city;
    }

    /**
     * @return boolean
     */
    public function isRemoved()
    {
        return $this->removed;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }
}
