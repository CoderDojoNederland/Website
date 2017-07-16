<?php

namespace CoderDojo\WebsiteBundle\Subscriber\EventCreatedEvent;

use CL\Slack\Model\Attachment;
use CL\Slack\Model\AttachmentField;
use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Event\DojoCreatedEvent;
use CoderDojo\WebsiteBundle\Event\EventCreatedEvent;
use CoderDojo\WebsiteBundle\Repository\DojoRepository;
use CoderDojo\WebsiteBundle\Service\SlackService;

class NotifySlack
{
    /**
     * @var SlackService
     **/
    private $slackService;

    /**
     * @var DojoRepository
     */
    private $dojoRepository;

    /**
     * NotifySlack constructor.
     * @param SlackService   $slackService
     * @param DojoRepository $dojoRepository
     */
    public function __construct(
        SlackService $slackService,
        DojoRepository $dojoRepository
    ) {
        $this->slackService = $slackService;
        $this->dojoRepository = $dojoRepository;
    }

    public function notify(EventCreatedEvent $event)
    {
        /** @var Dojo $dojo */
        $dojo = $this->dojoRepository->find($event->getDojoId());

        $dateFormatter = new \IntlDateFormatter(
            'nl_NL',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            'Europe/Amsterdam',
            \IntlDateFormatter::GREGORIAN,
            'dd MMMM Y'
        );

        $attachment = new Attachment();
        $attachment->setFallback(
            sprintf(
                'Er is een nieuw event toegevoegd in %s op %s! Inschrijven: %s',
                $dojo->getCity(),
                $dateFormatter->format($event->getDate()),
                $event->getUrl()
            )
        );
        $attachment->setText('Er is een nieuw event toegevoegd!');
        $attachment->setColor('good');

        $nameField = new AttachmentField();
        $nameField->setTitle('Naam');
        $nameField->setValue($event->getName());
        $nameField->setShort(true);

        $cityField = new AttachmentField();
        $cityField->setTitle('Locatie');
        $cityField->setValue($dojo->getCity());
        $cityField->setShort(true);

        $registerLinkField = new AttachmentField();
        $registerLinkField->setTitle('Inschrijven via:');
        $registerLinkField->setValue($event->getUrl());
        $registerLinkField->setShort(true);

        $dateField = new AttachmentField();
        $dateField->setTitle('Datum');
        $dateField->setValue($dateFormatter->format($event->getDate()));
        $dateField->setShort(true);

        $attachment->addField($nameField);
        $attachment->addField($cityField);
        $attachment->addField($registerLinkField);
        $attachment->addField($dateField);

        $this->slackService->sendToChannel('#general', '', [$attachment]);
    }
}
