<?php

namespace CoderDojo\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/online-les")
 */
class OnlineController extends Controller
{
    /**
     * @Route("/", name="online_course")
     */
    public function onlineCourseAction()
    {
        return $this->render(':Online:index.html.twig');
    }

    /**
     * @Route("/html-css", name="online_course_html_css")
     */
    public function htmlCssAction()
    {
        return $this->render(':Online/HTML:index.html.twig');
    }

    /**
     * @Route("/html-css/les-1", name="online_course_html_css_1")
     */
    public function htmlCss1Action()
    {
        return $this->render(':Online/HTML:course1.html.twig');
    }

    /**
     * @Route("/scratch", name="online_course_scratch")
     */
    public function scratchAction()
    {
        return $this->render(':Online/Scratch:index.html.twig');
    }

    /**
     * @Route("/scratch/versla-het-corona-monster", name="online_course_scratch_corona_monster")
     */
    public function scratch1Action()
    {
        return $this->render(':Online/Scratch:course1.html.twig');
    }

    /**
     * @Route("/python", name="online_course_python")
     */
    public function pythonAction()
    {
        return $this->render(':Online/Python:index.html.twig');
    }

    /**
     * @Route("/python/basis-programmeren", name="online_course_python_basic")
     */
    public function python1Action()
    {
        return $this->render(':Online/Python:course1.html.twig');
    }
}