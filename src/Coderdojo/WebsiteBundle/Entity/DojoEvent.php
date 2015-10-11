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
     * @param \DateTime $dojodate
     * @return DojoEvent
     */
    public function setDojodate($dojodate)
    {
        $this->date = $dojodate;
    
        return $this;
    }

    /**
     * Get dojodate
     *
     * @return \DateTime 
     */
    public function getDojodate()
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
     * Set date
     *
     * @param \Date $date
     * @return DojoEvent
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \Date
     */
    public function getDate()
    {
        return $this->date;
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
}