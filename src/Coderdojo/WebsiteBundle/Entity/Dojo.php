<?php

namespace Coderdojo\WebsiteBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Coderdojo\WebsiteBundle\Repository\DojoRepository")
 * @ORM\Table(name="Dojo")
 * @ORM\HasLifecycleCallbacks()
 */
class Dojo extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255)
     */
    protected $location;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=255)
     */
    protected $street;

    /**
     * @var string
     *
     * @ORM\Column(name="housenumber", type="string", length=255)
     */
    protected $housenumber;

    /**
     * @var string
     *
     * @ORM\Column(name="postalcode", type="string", length=255)
     */
    protected $postalcode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    protected $city;

    /**
     * @var decimal
     *
     * @ORM\Column(name="geo_lat", type="decimal", precision=9, scale=6)
     */
    protected $lat;

    /**
     * @var decimal
     *
     * @ORM\Column(name="geo_long", type="decimal", precision=9, scale=6)
     */
    protected $long;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook", type="string", length=255)
     */
    protected $facebook;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter", type="string", length=255)
     */
    protected $twitter;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255)
     */
    protected $website;

    /**
     * @var string
     *
     * @ORM\Column(name="organiser", type="string", length=255)
     */
    protected $organiser;

    /**
     * @ORM\OneToMany(targetEntity="DojoEvent", mappedBy="dojo")
     **/
    private $dojos;

    public function __construct()
    {
        parent::__construct();
        $this->dojos = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Dojo
     */
    public function setName($name)
    {
        $this->name = $name;
        $str = strtolower(trim($this->getName()));
        $str = preg_replace('/[^a-z0-9-]/', '-', $str);
        $str = preg_replace('/-+/', "-", $str);
        $this->setSlug($str);
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return Dojo
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set housenumber
     *
     * @param string $housenumber
     * @return Dojo
     */
    public function setHousenumber($housenumber)
    {
        $this->housenumber = $housenumber;

        return $this;
    }

    /**
     * Get housenumber
     *
     * @return string
     */
    public function getHousenumber()
    {
        return $this->housenumber;
    }

    /**
     * Set postalcode
     *
     * @param string $postalcode
     * @return Dojo
     */
    public function setPostalcode($postalcode)
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    /**
     * Get postalcode
     *
     * @return string
     */
    public function getPostalcode()
    {
        return $this->postalcode;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Dojo
     */
    public function setCity($city)
    {
        $this->city = $city;
        $this->slug = strtolower(str_replace(" ","-",$city));
        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set lat
     *
     * @param float $lat
     * @return Dojo
     */
    public function setLat($lat)
    {
      $this->lat = $lat;
      return $this;
    }

    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set long
     *
     * @param float $long
     * @return Dojo
     */
    public function setLong($long)
    {
      $this->long = $long;
      return $this;
    }

    /**
     * Get long
     *
     * @return float
     */
    public function getLong()
    {
        return $this->long;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Dojo
     */
    private function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     * @return Dojo
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     * @return Dojo
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Dojo
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return Dojo
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $email
     */
    public function setEmail($email){
        $this->setUsername($email);
        parent::setEmail($email);
    }

    /**
     * Set organiser
     *
     * @param string $organiser
     * @return Dojo
     */
    public function setOrganiser($organiser)
    {
        $this->organiser = $organiser;

        return $this;
    }

    /**
     * Get organiser
     *
     * @return string
     */
    public function getOrganiser()
    {
        return $this->organiser;
    }

    /**
     * Add dojos
     *
     * @param \Coderdojo\WebsiteBundle\Entity\DojoEvent $dojos
     * @return Dojo
     */
    public function addDojo(\Coderdojo\WebsiteBundle\Entity\DojoEvent $dojos)
    {
        $this->dojos[] = $dojos;

        return $this;
    }

    /**
     * Remove dojos
     *
     * @param \Coderdojo\WebsiteBundle\Entity\DojoEvent $dojos
     */
    public function removeDojo(\Coderdojo\WebsiteBundle\Entity\DojoEvent $dojos)
    {
        $this->dojos->removeElement($dojos);
    }

    /**
     * Get dojos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDojos()
    {
        return $this->dojos;
    }

    /**
     * @ORM\PreFlush()
     */
    public function geocodeAddress()
    {
      // build geocode request URL
      $geoCodeUrl  = sprintf("https://maps.googleapis.com/maps/api/geocode/json?address=%s+%s+%s+%s&key=%s",
        urlencode($this->street),
        urlencode($this->housenumber),
        urlencode($this->postalcode),
        urlencode($this->city),
        "AIzaSyAFfhAgoL1GV8iZZic3hy0uPKsrRhpVawE"
      );

      // make the request and decode the json
      $response = json_decode(file_get_contents($geoCodeUrl));

      // parse the results
      if(isset($response->results) && is_array($response->results)){
        if(isset($response->results[0]->geometry)){

          // everything is present, store it in the model
          $geometry   = $response->results[0]->geometry;
          $this->lat  = $geometry->location->lat;
          $this->long = $geometry->location->lng;
        }
      }
    }
}
