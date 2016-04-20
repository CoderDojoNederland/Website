<?php

namespace Coderdojo\WebsiteBundle\Controller;

use Coderdojo\WebsiteBundle\Entity\Dojo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    /**
     * @Route("/api/dojo/{id}", name="api_dojo")
     * @ParamConverter("dojo", class="CoderdojoWebsiteBundle:Dojo")
     */
    public function dojoAction(Dojo $dojo)
    {
        return new Response(
            json_encode($this->serializeDojo($dojo)),
            200,
            ['Access-Control-Allow-Origin' => '*']
        );
    }

    /**
     * @Route("/api/dojo/{id}/events", name="api_dojo_events")
     * @ParamConverter("dojo", class="CoderdojoWebsiteBundle:Dojo")
     */
    public function dojoEventAction(Dojo $dojo)
    {
        $events = $dojo->getDojos();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($events, 'json');

        return new Response(
            $jsonContent,
            200,
            ['Access-Control-Allow-Origin' => '*']
        );
    }

    /**
     * @Route("/api/dojos", name="api_dojos")
     */
    public function dojosAction()
    {
        $dojos = $this->getDoctrine()->getRepository("CoderdojoWebsiteBundle:Dojo")->getSortedByCity();
        $json = [];

        foreach ($dojos as $dojo) {
            $json[] = $this->serializeDojo($dojo);
        }

        return new Response(
            json_encode($json),
            200,
            ['Access-Control-Allow-Origin' => '*']
        );
    }

    /**
     * @Route("/api/events", name="api_events")
     */
    public function eventsAction()
    {
        $dojoEvents = $this->getDoctrine()->getRepository('CoderdojoWebsiteBundle:DojoEvent')->getAllUpcomingEvents();
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($dojoEvents, 'json');

        return new Response(
            $jsonContent,
            200,
            ['Access-Control-Allow-Origin' => '*']
        );
    }

    /**
     * Serialize dojo manually due to FosUserBundle adding password and salt
     * @TODO: Seperate dojo from user account
     *
     * @param Dojo $dojo
     * @return array
     */
    private function serializeDojo(Dojo $dojo)
    {
        return [
            "id" => $dojo->getId(),
            "email" => $dojo->getEmail(),
            "name" => $dojo->getName(),
            "location" => $dojo->getLocation(),
            "street" => $dojo->getStreet(),
            "housenumber" => $dojo->getHousenumber(),
            "postalcode" => $dojo->getPostalcode(),
            "city" => $dojo->getCity(),
            "lat" => $dojo->getLat(),
            "long" => $dojo->getLong(),
            "facebook" => $dojo->getFacebook(),
            "twitter" => $dojo->getTwitter(),
            "website" => $dojo->getFacebook()
        ];
    }
}