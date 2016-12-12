<?php

namespace CoderDojo\WebsiteBundle\Handler;

use CoderDojo\WebsiteBundle\Command\CreateDojoCommand;
use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Event\DojoCreatedEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SimpleBus\Message\Recorder\RecordsMessages;

class CreateDojoHandler
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
     * @param CreateDojoCommand $command
     */
    public function handle(CreateDojoCommand $command)
    {
        $internalDojo = new Dojo(
            $command->getZenId(),
            $command->getName(),
            $command->getCity(),
            $command->getLat(),
            $command->getLon(),
            $command->getEmail(),
            $command->getWebsite(),
            $command->getTwitter()
        );

        $internalDojo->setZenCreatorEmail($command->getZenCreatorEmail());
        $internalDojo->setZenUrl($command->getZenUrl());

        $this->doctrine->persist($internalDojo);

        $event = new DojoCreatedEvent(
            $command->getZenId(),
            $command->getZenCreatorEmail(),
            $command->getZenUrl(),
            $command->getName(),
            $command->getCity(),
            $command->getLat(),
            $command->getLon(),
            $command->getEmail(),
            $command->getWebsite(),
            $command->getTwitter(),
            $command->isRemoved()
        );

        $this->eventRecorder->record($event);
    }
}