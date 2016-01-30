<?php

namespace Coderdojo\WebsiteBundle\Controller;

use Coderdojo\WebsiteBundle\Form\Type\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Coderdojo\WebsiteBundle\Entity\DojoEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function homeAction()
    {
        $dojos = $this->getUpComingDojos();

        return $this->render('CoderdojoWebsiteBundle:Pages:index.html.twig', [
            'dojos' => $dojos
        ]);
    }

    /**
     * @Route("/over-coderdojo", name="about")
     */
    public function aboutAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:about.html.twig');
    }

    /**
     * @Route("/over-de-stichting", name="foundation")
     */
    public function foundationAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:stichting.html.twig');
    }

    /**
     * @Route("/opstarten", name="setup")
     */
    public function setupAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:setup.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
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

    /**
     * @Route("/dojos", name="dojos")
     */
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

    /**
     * @Route("/mijn-dojo/beheren", name="manage")
     */
    public function manageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $dojos = $em->getRepository("CoderdojoWebsiteBundle:DojoEvent")->findBy(
            array("dojo"=>$this->getUser()),
            array("date"=>"desc")
        );

        return $this->render('CoderdojoWebsiteBundle:Pages:manage.html.twig', array("dojos" => $dojos));
    }

    /**
     * @Route("/mijn-dojo/beheren/toevoegen", name="new-dojo")
     * @param Request $request
     * @return Response
     */
    public function manageAddAction(Request $request){
        $eid = $request->query->get('eid');
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

    /**
     * @Route("/api/dojos", name="list-dojos")
     */
    public function listDojosAction(){
        $dojos = $this->getUpComingDojos();

        return new JsonResponse($dojos);
    }

    /**
     * @Route("/materiaal", name="material")
     */
    public function materialAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:material.html.twig');
    }

    /**
     * @Route("/samenwerkingen", name="collaboration")
     */
    public function collaborationAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:collaboration.html.twig');
    }

    /**
     * @return array
     */
    private function getUpComingDojos()
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository("CoderdojoWebsiteBundle:DojoEvent");
        $query = $repo->createQueryBuilder('d')
            ->where('d.date > :today')
            ->setParameter('today', new \DateTime("now"))
            ->orderBy('d.date', 'ASC')
            ->getQuery();

        $nextDojos = $query->getResult();

        return $nextDojos;
    }
}
