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
     * @var string
     */
    private $status;

    /**
     * Event constructor.
     * @param string $zenId
     * @param string $zenDojoId
     * @param string $name
     * @param \DateTime $startTime
     * @param $status
     */
    public function __construct($zenId, $zenDojoId, $name, \DateTime $startTime, $status)
    {
        $this->zenId = $zenId;
        $this->zenDojoId = $zenDojoId;
        $this->name = $name;
        $this->startTime = new \DateTime($startTime->format("Y-m-d"));
        $this->status = $status;
    }

    public static function CreateFromEntity(DojoEvent $entity) {
        return new self(
            $entity->getZenId(),
            $entity->getDojo()->getZenId(),
            $entity->getName(),
            $entity->getDate(),
            'published'
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

    /**
     * @return bool
     */
    public function isPublished()
    {
        return $this->status === 'published';
    }
}