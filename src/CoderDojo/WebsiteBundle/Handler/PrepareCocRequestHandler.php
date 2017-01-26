<?php

namespace CoderDojo\WebsiteBundle\Handler;

use CoderDojo\WebsiteBundle\Event\CocRequestPreparedEvent;
use CoderDojo\WebsiteBundle\Command\PrepareCocRequestCommand;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SimpleBus\Message\Recorder\RecordsMessages;

class PrepareCocRequestHandler
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
    * @param PrepareCocRequestCommand $command
    * @return void
    */
    public function handle(PrepareCocRequestCommand $command)
    {
        $request = $this->doctrine->getRepository('CoderDojoWebsiteBundle:CocRequest')->find($command->getId());

        $request->prepared();

        $this->doctrine->getManager()->flush();

        $event = new CocRequestPreparedEvent($command->getId());
        $this->eventRecorder->record($event);
    }
}