<?php

namespace CoderDojo\WebsiteBundle\Handler;

use CoderDojo\WebsiteBundle\Command\ReceiveCocRequestCommand;
use CoderDojo\WebsiteBundle\Event\CocRequestReceivedEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SimpleBus\Message\Recorder\RecordsMessages;

class ReceiveCocRequestHandler
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
     * @param ReceiveCocRequestCommand $command
     * @return void
     */
    public function handle(ReceiveCocRequestCommand $command)
    {
        $request = $this->doctrine->getRepository('CoderDojoWebsiteBundle:CocRequest')->find($command->getId());

        $request->received();

        $this->doctrine->getManager()->flush();

        $event = new CocRequestReceivedEvent($command->getId());
        $this->eventRecorder->record($event);
    }
}