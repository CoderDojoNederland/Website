<?php

namespace CoderDojo\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use CoderDojo\WebsiteBundle\Entity\DojoEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DojoController extends Controller
{
    /**
     * @Route("/dojos", name="dojos")
     */
    public function dojosAction()
    {
        $dojos = $this->getDoctrine()->getRepository("CoderDojoWebsiteBundle:Dojo")->getSortedByCity();
        $nextDojos = $this->getDoctrine()->getRepository("CoderDojoWebsiteBundle:DojoEvent")->getAllUpcomingEvents();

        return $this->render('CoderDojoWebsiteBundle:Pages:dojos.html.twig', array("dojos" => $dojos, "nextdojos" => $nextDojos));
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboardAction()
    {
        return $this->render('CoderDojoWebsiteBundle:Dashboard:index.html.twig');
    }

    /**
     * @Route("/mijn-dojo/beheren/toevoegen", name="new-dojo")
     * @param Request $request
     * @return Response
     */
    public function manageAddAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $eid = $request->query->get('eid');
        
        if (0 === preg_match('/^[0-9]{9,15}$/', $eid)) {
            return new Response('Dit is geen geldig ID. Een id bestaat uit 10 cijfers, bijv. 11528212193.');
        }
        
        $url = "https://www.eventbriteapi.com/v3/events/".$eid."/?token=".$this->container->getParameter('eventbrite_api_token');
        $dojoEvent = $em->getRepository('CoderDojoWebsiteBundle:DojoEvent')->findOneBy(['eventbriteId'=>$eid]);

        if (null !== $dojoEvent) {
            return new Response('Dit event is al toegevoegd!');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        $result=curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);

        if(isset($result->error)){
            $this->get('logger')->addError('Error with EventBrite API for ' . $this->getUser()->getCity() . ' : ' . $result->error);
            $msg = "Er is iets mis gegaan. Wellicht klopt de Eventbrite id niet?";
        }else{
            if($result->organizer_id == $this->getUser()->getOrganiser())
            {
                $dojo = new DojoEvent();
                $dojo->setName($result->name->text)
                    ->setDate(new \DateTime($result->start->local))
                    ->setUrl($result->url)
                    ->setUser($this->getUser())
                    ->setEventbriteId($eid);
                $this->getUser()->addDojo($dojo);
                $em->persist($dojo);
                $em->flush();

                $msg = sprintf(
                    "%s heeft een nieuwe dojo toegevoegd voor %s\n<%s|Registreer op Eventbrite>",
                    $this->getUser()->getName(),
                    $dojo->getDate()->format('d F Y'),
                    $dojo->getUrl()
                );

                try {
                    $this->get('coder_dojo.website_bundle.slack_service')->sendToChannel('#general', $msg);
                } catch (\Exception $exception) {
                    // Fail silently so dojo's can still be added
                    $this->get('logger')->addError('Error with Slack for dojo ' . $this->getUser()->getCity() . ': ' . $exception->getMessage());
                }

                $msg = "ok";
            }else{
                $msg = "Deze dojo hoort niet bij jouw organizer id";
            }
        }

        return new Response($msg);
    }
}
