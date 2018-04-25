<?php

namespace CoderDojo\WebsiteBundle\Controller;

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
     * @Route("/samenwerkingen/code-qube", name="codeqube")
     */
    public function codeQubeAction()
    {
        return $this->render(':Pages:Samenwerkingen/codeqube.html.twig');
    }

    /**
     * @Route("/samenwerkingen/eu-code-week", name="eucodeweek")
     */
    public function euCodeWeekAction()
    {
        return $this->render(':Pages:Samenwerkingen/codeweekEU.html.twig');
    }

    /**
     * @Route("/samenwerkingen/boeken", name="books")
     */
    public function booksAction()
    {
        return $this->render(':Pages:Samenwerkingen/books.html.twig');
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
        return $this->render(':Pages:Achtergrond/newsletter.html.twig');
    }

    /******************
     * SUPPORTING
     ******************/

    /**
     * @Route("/space-games", name="space-games")
     */
    public function spaceGamesAction()
    {
        return $this->render(':Pages:Supporting/spaceGames.html.twig');
    }

    /**
     * @Route("/coolest-projects", name="coolest-projects")
     */
    public function coolestProjectsAction()
    {
        return $this->render(':Pages:Supporting/coolest-projects.html.twig');
    }

    /**
     * @Route("/ehbo-cursus", name="ehbo-cursus")
     */
    public function firstAidAction()
    {
        return $this->render(':Pages:Supporting/first-aid.html.twig');
    }

    /**
     * @Route("/privacy", name="privacy")
     */
    public function privacyAction()
    {
        return $this->render(':Pages:privacy.html.twig');
    }
}
