<?php

namespace CoderDojo\WebsiteBundle\Command;

use CoderDojo\WebsiteBundle\Entity\DojoEvent;

class CreateEventCommand
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string|null
     */
    private $zenId;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $dojoId;

    /**
     * CreateEventCommand constructor.
     * @param $dojoId
     * @param $name
     * @param $date
     * @param $url
     * @param null $zenId
     * @param string $type
     */
    public function __construct(
        $dojoId,
        $name,
        $date,
        $url,
        $zenId = null,
        $type = DojoEvent::TYPE_CUSTOM
    ) {
        $this->name = $name;
        $this->date = $date;
        $this->url = $url;
        $this->zenId = $zenId;
        $this->type = $type;
        $this->dojoId = $dojoId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return null|string
     */
    public function getZenId()
    {
        return $this->zenId;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getDojoId()
    {
        return $this->dojoId;
    }
}