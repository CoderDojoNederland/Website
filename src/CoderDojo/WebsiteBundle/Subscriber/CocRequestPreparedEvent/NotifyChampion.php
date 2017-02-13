<?php

namespace CoderDojo\WebsiteBundle\Subscriber\CocRequestPreparedEvent;

use CoderDojo\WebsiteBundle\Entity\CocRequest;
use CoderDojo\WebsiteBundle\Event\CocRequestCreatedEvent;
use CoderDojo\WebsiteBundle\Event\CocRequestPreparedEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Swift_Mailer;
use Symfony\Bundle\TwigBundle\TwigEngine;

class NotifyChampion
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var TwigEngine
     */
    private $templating;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * NotifySlack constructor.
     * @param Swift_Mailer $mailer
     * @param TwigEngine $templating
     * @param Registry $doctrine
     */
    public function __construct(
        Swift_Mailer $mailer,
        TwigEngine $templating,
        Registry $doctrine
    ) {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->doctrine = $doctrine;
    }

    public function notify(CocRequestPreparedEvent $event)
    {
        /** @var CocRequest $coc */
        $coc = $this->doctrine->getRepository(CocRequest::class)->find($event->getId());

        $message = \Swift_Message::newInstance()
            ->setSubject(sprintf('Status VOG aanvraag'))
            ->setFrom('contact@coderdojo.nl', 'CoderDojo Nederland')
            ->setReplyTo('contact@coderdojo.nl')
            ->setTo($coc->getRequestedBy()->getEmail())
            ->setCc($coc->getRequestedFor()->getEmail())
            ->setBcc('chris+vogrequest@coderdojo.nl')
            ->setContentType('text/html')
            ->setBody(
                $this->templating->render(
                    'CoderDojoWebsiteBundle:Dashboard:Email/Coc/prepared_champion.html.twig',
                    array(
                        'coc' => $coc
                    )
                )
            );

        $this->mailer->send($message);
    }
}