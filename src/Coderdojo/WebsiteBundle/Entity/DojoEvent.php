<?php

namespace Coderdojo\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DojoEvent
 *
 * @ORM\Table(name="DojoEvent")
 * @ORM\Entity
 */
class DojoEvent
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
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dojodate", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="eventbrite_id", type="string", length=255, unique=true)
     */
    private $eventbriteId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Dojo", inversedBy="dojos")
     * @ORM\JoinColumn(name="dojo_id", referencedColumnName="id")
     */
    private $dojo;


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
     * Set dojodate
     *
     * @param \DateTime $date
     * @return DojoEvent
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get dojodate
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return DojoEvent
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return DojoEvent
     */
    public function setName($name)
    {
        $this->name = $name;
    
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
     * Set dojo
     *
     * @param \Coderdojo\WebsiteBundle\Entity\Dojo $dojo
     * @return DojoEvent
     */
    public function setDojo(\Coderdojo\WebsiteBundle\Entity\Dojo $dojo = null)
    {
        $this->dojo = $dojo;
    
        return $this;
    }

    /**
     * Get dojo
     *
     * @return \Coderdojo\WebsiteBundle\Entity\Dojo 
     */
    public function getDojo()
    {
        return $this->dojo;
    }

    /**
     * @return string
     */
    public function getEventbriteId()
    {
        return $this->eventbriteId;
    }

    /**
     * @param string $eventbriteId
     */
    public function setEventbriteId($eventbriteId)
    {
        $this->eventbriteId = $eventbriteId;
    }
}