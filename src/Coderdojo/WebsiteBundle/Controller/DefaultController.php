<?php

namespace Coderdojo\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Coderdojo\WebsiteBundle\Entity\DojoEvent;
use Symfony\Component\HttpFoundation\Response;

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

        $repo = $em->getRepository("CoderdojoWebsiteBundle:DojoEvent");
        $query = $repo->createQueryBuilder('d')
            ->where('d.date > :today')
            ->setParameter('today', new \DateTime("now"))
            ->orderBy('d.date', 'ASC')
            ->getQuery();

        $nextDojos = $query->getResult();

        return $this->render('CoderdojoWebsiteBundle:Pages:dojos.html.twig', array("dojos" => $dojos, "nextdojos"=>$nextDojos));
    }

    public function dojoAction($city)
    {
        $em = $this->getDoctrine()->getManager();
        $dojo = $em->getRepository("CoderdojoWebsiteBundle:Dojo")->findOneBySlug($city);

        return $this->render('CoderdojoWebsiteBundle:Pages:dojo.html.twig', array("dojo" => $dojo));
    }

    public function manageAction(){
        //$dojos = $this->getUser()->getDojos();

        $em = $this->getDoctrine()->getManager();
        $dojos = $em->getRepository("CoderdojoWebsiteBundle:DojoEvent")->findBy(
            array("dojo"=>$this->getUser()),
            array("date"=>"desc")
        );

        return $this->render('CoderdojoWebsiteBundle:Pages:manage.html.twig', array("dojos" => $dojos));
    }

    public function manageAddAction(){
        $eid = $this->getRequest()->query->get('eid');
        $url = "https://www.eventbriteapi.com/v3/events/".$eid."/?token=CT3M6TIFGKYO5CM7QWOK";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        $result=curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        if(isset($result->error)){
            $msg = "Er is iets mis gegaan. Wellicht klopt de Eventbrite id niet?";
        }else{
            if($result->organizer_id == $this->getUser()->getOrganiser())
            {
                $em = $this->getDoctrine()->getManager();
                $dojo = new DojoEvent();
                $dojo->setName($result->name->text)
                    ->setDate(new \DateTime($result->start->local))
                    ->setUrl($result->url)
                    ->setDojo($this->getUser());
                $this->getUser()->addDojo($dojo);
                $em->persist($dojo);
                $em->flush();
                $msg = "ok";
            }else{
                $msg = "Deze dojo hoort niet bij jouw organizer id";
            }
        }

        return new Response($msg);
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
            $event['time'] = $result->events[0]->start->local;
            return $event;
        }
        else{
            return false;
        }
    }
}
