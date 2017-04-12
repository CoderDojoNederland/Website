<?php

namespace CoderDojo\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/nieuws")
 */
class BlogController extends Controller
{
    /**
     * @Route("/artikelen", name="admin_blog_articles")
     */
    public function indexAction()
    {
        return $this->render('AdminBundle::index.html.twig');
    }
}
