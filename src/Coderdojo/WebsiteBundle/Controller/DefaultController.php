<?php

namespace Coderdojo\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Default:index.html.twig');
    }
}
