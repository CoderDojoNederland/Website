<?php

namespace CoderDojo\WebsiteBundle\Handler;

use CoderDojo\WebsiteBundle\Command\RemoveEventCommand;
use CoderDojo\WebsiteBundle\Event\DojoRemovedEvent;
use CoderDojo\WebsiteBundle\Event\EventRemovedEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SimpleBus\Message\Recorder\RecordsMessages;

class RemoveEventHandler
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
     * @param RemoveEventCommand $command
     */
    public function handle(RemoveEventCommand $command)
    {
        $event = $this->doctrine->getRepository('CoderDojoWebsiteBundle:DojoEvent')->findOneBy(['zenId' => $command->getId()]);

        $this->doctrine->remove($event);
        $this->doctrine->flush();

        $event = new EventRemovedEvent(
            $command->getId()
        );

        $this->eventRecorder->record($event);
    }
}