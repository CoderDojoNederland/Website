<?php

namespace CoderDojo\WebsiteBundle\Handler;

use CoderDojo\WebsiteBundle\Event\CocRequestShippedEvent;
use CoderDojo\WebsiteBundle\Command\ShipCocRequestCommand;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SimpleBus\Message\Recorder\RecordsMessages;

class ShipCocRequestHandler
{
    /**
     * @var RecordsMessages
     */
    private $eventRecorder;
    
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(
        RecordsMessages $eventRecorder,
        Registry $doctrine
    ) {
        $this->eventRecorder = $eventRecorder;
        $this->doctrine = $doctrine;
    }

    /**
    * Handles the ShipCocRequestCommand logic
    *
    * @param ShipCocRequestCommand $command
    * @return void
    */
    public function handle(ShipCocRequestCommand $command)
    {
        $request = $this->doctrine->getRepository('CoderDojoWebsiteBundle:CocRequest')->find($command->getId());

        $request->requested();

        $this->doctrine->getManager()->flush();

        $event = new CocRequestShippedEvent($command->getId());
        $this->eventRecorder->record($event);
    }
}