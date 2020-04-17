<?php

namespace CoderDojo\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

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
}