<?php

namespace CoderDojo\WebsiteBundle\Handler;

use CoderDojo\WebsiteBundle\Event\CocRequestCreatedEvent;
use CoderDojo\WebsiteBundle\Command\CreateCocRequestCommand;
use CoderDojo\WebsiteBundle\Entity\CocRequest;
use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SimpleBus\Message\Recorder\RecordsMessages;

class CreateCocRequestHandler
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
    * Handles the CreateCocRequestCommand logic
    *
    * @param CreateCocRequestCommand $command
    * @return void
    */
    public function handle(CreateCocRequestCommand $command)
    {
        $user = $this->doctrine->getRepository(User::class)->find($command->getUserId());
        $dojo = $this->doctrine->getRepository(Dojo::class)->find($command->getDojoId());

        $cocRequest = new CocRequest(
            $command->getId(),
            $command->getLetters(),
            $command->getName(),
            $command->getEmail(),
            $command->getNotes(),
            $user,
            $dojo
        );

        $this->doctrine->getManager()->persist($cocRequest);
        $this->doctrine->getManager()->flush();

        $event = new CocRequestCreatedEvent(
            $cocRequest->getId(),
            $cocRequest->getLetters(),
            $cocRequest->getName(),
            $cocRequest->getEmail(),
            $cocRequest->getNotes(),
            $cocRequest->getRequestedBy()->getId(),
            $cocRequest->getRequestedFor()->getId()
        );
        $this->eventRecorder->record($event);
    }
}