<?php

namespace CoderDojo\WebsiteBundle\Controller;

use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Form\Type\NewsletterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PagesController extends Controller
{
    /******************
     * MEEHELPEN
     ******************/

    /**
     * @Route("/meehelpen/opstarten", name="setup")
     */
    public function setupAction()
    {
        return $this->render(':Pages:Meehelpen/setup.html.twig');
    }

    /**
     * @Route("/meehelpen/mentor-worden", name="mentors")
     */
    public function mentorAction()
    {
        return $this->render(':Pages:Meehelpen/mentor.html.twig');
    }

    /**
     * @Route("/meehelpen/materiaal", name="material")
     */
    public function materialAction()
    {
        return $this->render(':Pages:Meehelpen/material.html.twig');
    }

    /******************
     * Samenwerkingen
     ******************/
    /**
     * @Route("/codeweek", name="codeweek")
     */
    public function euCodeWeekAction()
    {
        $events = $this->getDoctrine()->getRepository("CoderDojoWebsiteBundle:DojoEvent")->getCodeWeek2018Events();
        return $this->render(':Pages:codeweek.html.twig', ['events' => $events]);
    }

    /******************
     * De Stichting
     ******************/

    /**
     * @Route("/informatie/over-coderdojo", name="about")
     */
    public function aboutAction()
    {
        return $this->render(':Pages:Achtergrond/about.html.twig');
    }

    /**
     * @Route("/informatie/over-de-stichting", name="foundation")
     */
    public function foundationAction()
    {
        return $this->render(':Pages:Achtergrond/stichting.html.twig');
    }

    /**
     * @Route("/informatie/nieuwsbrief", name="newsletter")
     */
    public function newsletterAction()
    {
        return $this->render(':Pages:newsletter.html.twig');
    }

    /******************
     * SUPPORTING
     ******************/

    /**
     * @Route("/privacy", name="privacy")
     */
    public function privacyAction()
    {
        return $this->render(':Pages:privacy.html.twig');
    }
}
