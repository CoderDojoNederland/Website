<?php

namespace Coderdojo\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * DojoEvent
 *
 * @ORM\Table(name="DojoEvent")
 * @ORM\Entity(repositoryClass="Coderdojo\WebsiteBundle\Repository\DojoEventRepository")
 * @ExclusionPolicy("none")
 */
class DojoEvent
{
    const TYPE_ZEN = 'zen';
    const TYPE_CUSTOM = 'custom';

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
     *
     * @ORM\Column(name="event_type", type="string", length=255, options={"default" : "custom"})
     */
    private $type;

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
     * @ORM\Column(name="eventbrite_id", type="string", length=255, unique=true, nullable=true)
     */
    private $eventbriteId;

    /**
     * @var Dojo
     *
     * @ORM\ManyToOne(targetEntity="Dojo", inversedBy="events")
     * @ORM\JoinColumn(name="dojo_id", referencedColumnName="id")
     * @Serializer\Accessor(getter="getDojoId")
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
     * @return int
     */
    public function getDojoId()
    {
        return $this->getDojo()->getId();
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

    /**
     * @return Dojo
     */
    public function getDojo()
    {
        return $this->dojo;
    }

    /**
     * @param Dojo $dojo
     */
    public function setDojo($dojo)
    {
        $this->dojo = $dojo;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getZenId()
    {
        return $this->zenId;
    }

    /**
     * @param string $zenId
     */
    public function setZenId($zenId)
    {
        $this->zenId = $zenId;
    }
}