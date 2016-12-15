<?php

namespace CoderDojo\WebsiteBundle\Subscriber\DojoCreatedEvent;

use CL\Slack\Model\Attachment;
use CL\Slack\Model\AttachmentField;
use CoderDojo\WebsiteBundle\Event\DojoCreatedEvent;
use CoderDojo\WebsiteBundle\Service\SlackService;

class NotifySlack
{
    /**
     * @var SlackService
     **/
    private $slackService;

    /**
     * NotifySlack constructor.
     * @param SlackService $slackService
     */
    public function __construct(SlackService $slackService)
    {
        $this->slackService = $slackService;
    }

    public function notify(DojoCreatedEvent $event)
    {
        $attachment = new Attachment();
        $attachment->setFallback(
            sprintf(
                'Er is een nieuwe dojo toegevoegd! %s (Website: $s)',
                $event->getName(),
                $event->getWebsite()
            )
        );
        $attachment->setText('Er is een nieuwe dojo toegevoegd, welkom! Is dit jouw dojo? Claim hem op coderdojo.nl');
        $attachment->setColor('good');

        $nameField = new AttachmentField();
        $nameField->setTitle('Name');
        $nameField->setValue($event->getName());
        $nameField->setShort(true);

        $cityField = new AttachmentField();
        $cityField->setTitle('City');
        $cityField->setValue($event->getCity());
        $cityField->setShort(true);

        $emailField = new AttachmentField();
        $emailField->setTitle('Email:');
        $emailField->setValue($event->getEmail());
        $emailField->setShort(true);

        $websiteField = new AttachmentField();
        $websiteField->setTitle('Website');
        $websiteField->setValue($event->getWebsite());
        $websiteField->setShort(true);

        $attachment->addField($nameField);
        $attachment->addField($cityField);
        $attachment->addField($emailField);
        $attachment->addField($websiteField);

        $this->slackService->sendToChannel('#website-nl', '', [$attachment]);
    }
}