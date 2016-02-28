<?php

namespace Coderdojo\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Coderdojo\WebsiteBundle\Entity\DojoEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DojoController extends Controller
{
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
}
