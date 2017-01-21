<?php

namespace CoderDojo\WebsiteBundle\Handler;

use CoderDojo\WebsiteBundle\Command\CocRequestCreatedEvent;
use CoderDojo\WebsiteBundle\Command\CreateCocRequestCommand;
use CoderDojo\WebsiteBundle\Entity\CocRequest;
use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use SimpleBus\Message\Recorder\RecordsMessages;

class CreateCocRequestHandler
{
    /**
     * @var RecordsMessages
     */
    private $eventRecorder;
    
    /**
     * @var EntityManagerInterface
     */
    private $doctrine;

    public function __construct(
        RecordsMessages $eventRecorder,
        EntityManagerInterface $doctrine
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

        $this->doctrine->persist($cocRequest);
        $this->doctrine->flush();

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