<?php

namespace CoderDojo\CliBundle\Service\ZenModel;

use CoderDojo\WebsiteBundle\Entity\DojoEvent;

class Event
{
    /**
     * @var string
     */
    private $zenId;

    /**
     * @var string
     */
    private $zenDojoId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $startTime;

    /**
     * Event constructor.
     * @param string $zenId
     * @param string $zenDojoId
     * @param string $name
     * @param \DateTime $startTime
     */
    public function __construct($zenId, $zenDojoId, $name, \DateTime $startTime)
    {
        $this->zenId = $zenId;
        $this->zenDojoId = $zenDojoId;
        $this->name = $name;
        $this->startTime = new \DateTime($startTime->format("Y-m-d"));
    }

    public static function CreateFromEntity(DojoEvent $entity) {
        return new self(
            $entity->getZenId(),
            $entity->getDojo()->getZenId(),
            $entity->getName(),
            $entity->getDate()
        );
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return string
     */
    public function getZenDojoId()
    {
        return $this->zenDojoId;
    }
}