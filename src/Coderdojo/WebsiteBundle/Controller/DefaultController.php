<?php

namespace Coderdojo\WebsiteBundle\Controller;

use Coderdojo\WebsiteBundle\Form\Type\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Coderdojo\WebsiteBundle\Entity\DojoEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    public function contactAction(Request $request)
    {
        $form = $this->createForm(new ContactFormType());

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Email via Website')
                    ->setFrom($form->get('email')->getData(), $form->get('naam')->getData())
                    ->setTo('contact@coderdojo.nl')
                    ->setContentType('text/html')
                    ->setBody(
                        $this->renderView(
                            'CoderdojoWebsiteBundle::contactmail.html.twig',
                            array(
                                'naam' => $form->get('naam')->getData(),
                                'message' => $form->get('message')->getData()
                            )
                        )
                    );

                $this->get('mailer')->send($message);

                $request->getSession()->getFlashBag()->add('success', 'Bedankt voor je bericht!');

                return $this->redirect($this->generateUrl('contact'));
            }
        }

        return $this->render('CoderdojoWebsiteBundle:Pages:contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function dojosAction()
    {
        $dojos = $this->getDoctrine()->getRepository("CoderdojoWebsiteBundle:Dojo")->findAll();

        usort($dojos, function ($a, $b) {
            return strnatcmp($a->getCity(), $b->getCity());
        });

        $repo = $this->getDoctrine()->getRepository("CoderdojoWebsiteBundle:DojoEvent");
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

                $msg = sprintf(
                    "%s heeft een nieuwe dojo toegevoegd voor %s\n<%s|Registreer op Eventbrite>",
                    $this->getUser()->getName(),
                    $dojo->getDate()->format('d F Y'),
                    $dojo->getUrl()
                );

                $this->get('coderdojo.website_bundle.slack_service')->sendToChannel('#general', $msg);

                $msg = "ok";
            }else{
                $msg = "Deze dojo hoort niet bij jouw organizer id";
            }
        }

        return new Response($msg);
    }

    public function listDojosAction(){
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository("CoderdojoWebsiteBundle:DojoEvent");
        $query = $repo->createQueryBuilder('d')
            ->where('d.date > :today')
            ->setParameter('today', new \DateTime("now"))
            ->orderBy('d.date', 'ASC')
            ->getQuery();

        $nextDojos = $query->getResult();

        $dojos = array();

        foreach($nextDojos as $dojo){
            $datestring = $dojo->getDate()->format("m-d-Y");
            $dojos[] = array(
                "name" => $dojo->getName(),
                "date" => $datestring,
                "url"  => $dojo->getUrl(),
                "dojo" => $dojo->getDojo()->getName(),
                "dojoid" => $dojo->getDojo()->getId(),
                "location" => $dojo->getDojo()->getLocation()
            );
        }

        return new JsonResponse($dojos);
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
