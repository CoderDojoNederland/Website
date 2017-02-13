<?php

namespace CoderDojo\WebsiteBundle\Subscriber\CocRequestShippedEvent;

use CoderDojo\WebsiteBundle\Entity\CocRequest;
use CoderDojo\WebsiteBundle\Event\CocRequestCreatedEvent;
use CoderDojo\WebsiteBundle\Event\CocRequestShippedEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Swift_Mailer;
use Symfony\Bundle\TwigBundle\TwigEngine;

class NotifyFoundation
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

    public function notify(CocRequestShippedEvent $event)
    {
        /** @var CocRequest $coc */
        $coc = $this->doctrine->getRepository(CocRequest::class)->find($event->getId());

        $message = \Swift_Message::newInstance()
            ->setSubject(sprintf('VOG Onderweg'))
            ->setFrom('contact@coderdojo.nl', 'CoderDojo Nederland')
            ->setReplyTo($coc->getEmail())
            ->setTo('contact@coderdojo.nl')
            ->setContentType('text/html')
            ->setBody(
                $this->templating->render(
                    'CoderDojoWebsiteBundle:Dashboard:Email/Coc/shipped_foundation.html.twig',
                    array(
                        'coc' => $coc
                    )
                )
            );

        $this->mailer->send($message);
    }
}