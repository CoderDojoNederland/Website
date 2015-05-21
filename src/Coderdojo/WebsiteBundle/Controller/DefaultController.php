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

        $nextDojos = array();
        foreach($dojos as $dojo){
            if($dojo->getOrganiser() != ''){
                $nextDojo = $this->getNextDojo($dojo->getOrganiser());
                if($nextDojo){
                    $nextDojo['name'] = $dojo->getName();
                    $nextDojos[] = $nextDojo;
                }
            }
        }

        return $this->render('CoderdojoWebsiteBundle:Pages:dojos.html.twig', array("dojos" => $dojos, "nextdojos"=>$nextDojos));
    }

    public function dojoAction($city)
    {
        $em = $this->getDoctrine()->getManager();
        $dojo = $em->getRepository("CoderdojoWebsiteBundle:Dojo")->findOneBySlug($city);

        return $this->render('CoderdojoWebsiteBundle:Pages:dojo.html.twig', array("dojo" => $dojo));
    }

    private function getNextDojo($organiserid){

        $url = "https://www.eventbriteapi.com/v3/events/search/?token=CT3M6TIFGKYO5CM7QWOK&organizer.id=".$organiserid;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        $result=curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        if($result->pagination->object_count > 0){
            //var_dump($result->events[0]->url);
            //echo "<br/>";
            //var_dump($result->events[0]->start->local);

            $event['url'] = $result->events[0]->url;
            $event['time'] = date_parse($result->events[0]->start->local);
            return $event;
        }
        else{
            return false;
        }
    }
}
