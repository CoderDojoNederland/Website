<?php

namespace CoderDojo\WebsiteBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * Dojo
 *
 * @ORM\Table(name="Dojo")
 * @ORM\Entity(repositoryClass="CoderDojo\WebsiteBundle\Repository\DojoRepository")
 * @ExclusionPolicy("none")
 */
class Dojo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $zenId;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $zenCreatorEmail;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $zenUrl;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=9, scale=6)
     */
    private $lat;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=9, scale=6)
     */
    private $lon;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $website;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitter;

    /**
     * @var DojoEvent[]
     * @ORM\OneToMany(targetEntity="DojoEvent", mappedBy="dojo")
     **/
    private $events;

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="User", inversedBy="dojos")
     * @ORM\JoinTable(name="users_dojos")
     **/
    private $owners;

    /**
     * Dojo constructor.
     * @param $zenId
     * @param $name
     * @param $city
     * @param $lat
     * @param $lon
     * @param $email
     * @param $website
     * @param $twitter
     * @param User $owner
     */
    public function __construct(
        $zenId,
        $name,
        $city,
        $lat,
        $lon,
        $email,
        $website,
        $twitter,
        User $owner = null
    ) {
        $this->zenId = $zenId;
        $this->name = $name;
        $this->city = $city;
        $this->lat = $lat;
        $this->lon = $lon;
        $this->email = $email;
        $this->setWebsite($website);
        $this->setTwitter($twitter);

        $this->owners = new ArrayCollection();
        $this->events = new ArrayCollection();

        if (null != $owner) {
            $this->owners->add($owner);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param float $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return float
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * @param float $lon
     */
    public function setLon($lon)
    {
        $this->lon = $lon;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        if (null === $this->website) {
            return 'https://coderdojo.nl';
        }

        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        if (null === $website) {
            $this->website = 'https://coderdojo.nl';

            return;
        }

        $this->website = $website;
    }

    /**
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * @param string $twitter
     */
    public function setTwitter($twitter)
    {
        $twitter = str_replace('@', '', $twitter);
        $twitter = str_replace('https://twitter.com/', '', $twitter);
        $this->twitter = $twitter;
    }

    /**
     * @return DojoEvent[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param DojoEvent $event
     */
    public function addEvent(DojoEvent $event)
    {
        $this->events->add($event);
    }

    /**
     * @return User[]
     */
    public function getOwners()
    {
        return $this->owners;
    }

    /**
     * @param User $owner
     */
    public function addOwner(User $owner)
    {
        $this->owners->add($owner);
    }

    /**
     * @return string
     */
    public function getZenId()
    {
        return $this->zenId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getZenCreatorEmail()
    {
        return $this->zenCreatorEmail;
    }

    /**
     * @param string $zenCreatorEmail
     */
    public function setZenCreatorEmail($zenCreatorEmail)
    {
        $this->zenCreatorEmail = $zenCreatorEmail;
    }

    /**
     * @param string $zenId
     */
    public function setZenId($zenId)
    {
        $this->zenId = $zenId;
    }

    /**
     * @return string
     */
    public function getZenUrl()
    {
        return $this->zenUrl;
    }

    /**
     * @param string $zenUrl
     */
    public function setZenUrl($zenUrl)
    {
        $this->zenUrl = 'https://zen.coderdojo.com/dojo/'.$zenUrl;
    }
}