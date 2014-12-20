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

    public function codeeuAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:codeweek.html.twig');
    }

    public function setupAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:setup.html.twig');
    }

    public function dojosAction()
    {
        $em = $this->getDoctrine()->getManager();
        $dojos = $em->getRepository("CoderdojoWebsiteBundle:Dojo")->findAll();

        $next = $em->getRepository("CoderdojoWebsiteBundle:Dojo");
        $query = $next->createQueryBuilder('p')
            ->where('p.next > :today')
            ->setParameter('today', date("Y/m/d H:I:s"))
            ->orderBy('p.next', 'ASC')
            ->getQuery();

        $nextdojos = $query->getResult();

        return $this->render('CoderdojoWebsiteBundle:Pages:dojos.html.twig', array("dojos" => $dojos, "nextdojos"=>$nextdojos));
    }

    public function dojoAction($city)
    {
        $em = $this->getDoctrine()->getManager();
        $dojo = $em->getRepository("CoderdojoWebsiteBundle:Dojo")->findOneBySlug($city);

        return $this->render('CoderdojoWebsiteBundle:Pages:dojo.html.twig', array("dojo" => $dojo));
    }
}
