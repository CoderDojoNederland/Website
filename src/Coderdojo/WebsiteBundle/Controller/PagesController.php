<?php

namespace Coderdojo\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PagesController extends Controller
{
    /******************
     * MEEDOEN
     ******************/

    /**
     * @Route("/meedoen/opstarten", name="setup")
     */
    public function setupAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:setup.html.twig');
    }

    /**
     * @Route("/meedoen/mentor-worden", name="mentors")
     */
    public function mentorAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:mentor.html.twig');
    }

    /**
     * @Route("/meedoen/materiaal", name="material")
     */
    public function materialAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:material.html.twig');
    }

    /******************
     * ACHTERGROND
     ******************/

    /**
     * @Route("/informatie/over-coderdojo", name="about")
     */
    public function aboutAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:about.html.twig');
    }

    /**
     * @Route("/informatie/over-de-stichting", name="foundation")
     */
    public function foundationAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:stichting.html.twig');
    }

    /**
     * @Route("/informatie/samenwerkingen", name="collaboration")
     */
    public function collaborationAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:collaboration.html.twig');
    }
}
