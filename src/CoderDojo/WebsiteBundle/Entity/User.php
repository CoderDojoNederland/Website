<?php

namespace CoderDojo\WebsiteBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
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
     * @var Dojo[]
     * @ORM\ManyToMany(targetEntity="Dojo", mappedBy="owners")
     **/
    private $dojos;

    /**
     * @var DojoEvent[]
     * @ORM\OneToMany(targetEntity="DojoEvent", mappedBy="user")
     **/
    private $events;

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
    private $long;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $website;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $twitter;

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
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->setUsername($email);
        parent::setEmail($email);
    }

    /**
     * Add dojos
     *
     * @param Dojo $dojo
     * @return User
     */
    public function addDojo(Dojo $dojo)
    {
        $this->dojos[] = $dojo;

        return $this;
    }

    /**
     * Remove dojo
     *
     * @param Dojo $dojo
     */
    public function removeDojo(Dojo $dojo)
    {
        $this->dojos->removeElement($dojo);
    }

    /**
     * Get dojos
     *
     * @return Dojo[]
     */
    public function getDojos()
    {
        return $this->dojos;
    }

    /**
     * @return float
     */
    public function getLong()
    {
        return $this->long;
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
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
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
        $this->twitter = $twitter;
    }

    /**
     * @return DojoEvent[]
     */
    public function getEvents()
    {
        return $this->events;
    }
}
