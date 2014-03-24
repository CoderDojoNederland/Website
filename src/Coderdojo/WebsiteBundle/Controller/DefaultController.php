<?php

namespace Coderdojo\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:index.html.twig');
    }

    public function aboutAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:about.html.twig');
    }
}
