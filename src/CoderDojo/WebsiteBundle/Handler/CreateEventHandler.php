<?php

namespace CoderDojo\WebsiteBundle\Handler;

use CoderDojo\WebsiteBundle\Command\CreateEventCommand;
use CoderDojo\WebsiteBundle\Entity\DojoEvent;
use CoderDojo\WebsiteBundle\Event\EventCreatedEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SimpleBus\Message\Recorder\RecordsMessages;

class CreateEventHandler
{
    /**
     * @var RecordsMessages
     */
    private $eventRecorder;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * CreateDojoHandler constructor.
     * @param RecordsMessages $eventRecorder
     * @param Registry $doctrine
     */
    public function __construct(RecordsMessages $eventRecorder, Registry $doctrine)
    {
        $this->eventRecorder = $eventRecorder;
        $this->doctrine = $doctrine->getManager();
    }

    /**
     * @param CreateEventCommand $command
     */
    public function handle(CreateEventCommand $command)
    {
        $dojo = $this->doctrine->getRepository('CoderDojoWebsiteBundle:Dojo')->find($command->getDojoId());

        $event = new DojoEvent();
        $event->setName($command->getName());
        $event->setDate($command->getDate());
        $event->setEventType($command->getType());
        $event->setZenId($command->getZenId());
        $event->setUrl($command->getUrl());
        $event->setDojo($dojo);
        $dojo->addEvent($event);

        $this->doctrine->persist($event);
        $this->doctrine->flush();

        $event = new EventCreatedEvent(
            $command->getDojoId(),
            $command->getName(),
            $command->getDate(),
            $command->getUrl(),
            $command->getZenId(),
            $command->getType()
        );

        $this->eventRecorder->record($event);
    }
}