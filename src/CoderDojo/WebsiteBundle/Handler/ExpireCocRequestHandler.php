<?php

namespace CoderDojo\WebsiteBundle\Handler;

use CoderDojo\WebsiteBundle\Command\ExpireCocRequestCommand;
use CoderDojo\WebsiteBundle\Entity\CocRequest;
use CoderDojo\WebsiteBundle\Event\CocRequestExpiredEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SimpleBus\Message\Recorder\RecordsMessages;

class ExpireCocRequestHandler
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
    * Handles the PrepareCocRequestCommand logic
    *
    * @param ExpireCocRequestCommand $command
    * @return void
    */
    public function handle(ExpireCocRequestCommand $command)
    {
        /** @var CocRequest $request */
        $request = $this->doctrine->getRepository('CoderDojoWebsiteBundle:CocRequest')->find($command->getId());

        $request->expired();

        $this->doctrine->getManager()->flush();

        $event = new CocRequestExpiredEvent($command->getId());
        $this->eventRecorder->record($event);
    }
}
