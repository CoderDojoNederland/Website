<?php

namespace CoderDojo\WebsiteBundle\Handler;

use CoderDojo\WebsiteBundle\Command\RemoveDojoCommand;
use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Event\DojoCreatedEvent;
use CoderDojo\WebsiteBundle\Event\DojoRemovedEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SimpleBus\Message\Recorder\RecordsMessages;

class RemoveDojoHandler
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
     * @param RemoveDojoCommand $command
     */
    public function handle(RemoveDojoCommand $command)
    {
        $dojo = $this->doctrine->getRepository('CoderDojoWebsiteBundle:Dojo')->find($command->getId());

        $this->doctrine->remove($dojo);
        $this->doctrine->flush();

        $event = new DojoRemovedEvent(
            $command->getId()
        );

        $this->eventRecorder->record($event);
    }
}