<?php

namespace CoderDojo\WebsiteBundle\Controller;

use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Entity\DojoRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use CoderDojo\WebsiteBundle\Entity\DojoEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/dashboard")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     */
    public function dashboardAction()
    {
        return $this->render('CoderDojoWebsiteBundle:Dashboard:dashboard.html.twig');
    }

    /**
     * @Route("/add-dojo/{dojoId}", name="dashboard-request-dojo")
     */
    public function requestDojoAction($dojoId)
    {
        $dojo = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:Dojo')->find($dojoId);

        if (null === $dojo) {
            $this->get('session')->getFlashBag()->add('error', 'De dojo waar je toegang toe wilt kan niet gevonden worden.');

            return $this->redirectToRoute('dashboard-add-dojo');
        }

        $dojoRequest = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:DojoRequest')->findOneBy([
            'dojo' => $dojo,
            'user' => $this->getUser()
        ]);

        if (null !== $dojoRequest) {
            $this->get('session')->getFlashBag()->add('error', 'Er is voor jou al een verzoek gestuurd naar deze dojo.');

            return $this->redirectToRoute('dashboard-add-dojo');
        }

        $dojoRequest = new DojoRequest($dojo, $this->getUser());
        $this->getUser()->addDojoRequest($dojoRequest);
        $dojo->addMentorRequest($dojoRequest);

        $this->getDoctrine()->getManager()->persist($dojoRequest);
        $this->getDoctrine()->getManager()->flush();

        /**
         * Send email to dojo contact address
         */
        $message = \Swift_Message::newInstance()
            ->setSubject(sprintf('%s wilt toegang tot %s', $this->getUser()->getFirstname(), $dojo->getName()))
            ->setFrom('no-reply@coderdojo.nl', sprintf('%s %s', $this->getUser()->getFirstname(), $this->getUser()->getLastname()))
            ->setReplyTo($this->getUser()->getEmail())
            ->setTo($dojo->getEmail())
            ->setBcc('chris+dojorequest@coderdojo.nl')
            ->setContentType('text/html')
            ->setBody(
                $this->renderView(
                    'CoderDojoWebsiteBundle:Dashboard:requestmail.html.twig',
                    array(
                        'dojo' => $dojo,
                        'user' => $this->getUser()
                    )
                )
            );

        $this->get('mailer')->send($message);

        $this->get('session')->getFlashBag()->add('success', 'We hebben een email gestuurd naar deze dojo met het verzoek om jou toegang te geven.');

        return $this->render('CoderDojoWebsiteBundle:Dashboard:add-dojo.html.twig');
    }

    /**
     * @Route("/mentor-requests", name="dashboard-mentor-requests")
     */
    public function mentorRequestsAction()
    {
        /** @var Dojo[] $dojos */
        $dojos = $this->getUser()->getDojos();

        $requests = [];
        foreach ($dojos as $dojo) {
            foreach ($dojo->getMentorRequests() as $mentorRequest) {
                if (null === $mentorRequest->getApproved()) {
                    $requests[] = $mentorRequest;
                }
            }
        }

        return $this->render('CoderDojoWebsiteBundle:Dashboard:mentor-requests.html.twig', ['requests' => $requests]);
    }

    /**
     * @Route("/mentor-requests/{id}", name="dashboard-mentor-requests-accept")
     */
    public function mentorAcceptAction($id)
    {
        $mentorRequest = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:DojoRequest')->find($id);

        if (null === $mentorRequest) {
            $this->get('session')->getFlashBag()->add('error', 'Dit verzoek kon niet worden gevonden om te accepteren.');

            return $this->redirectToRoute('dashboard-mentor-requests');
        }

        if (false === $mentorRequest->getDojo()->getOwners()->contains($this->getUser())) {
            $this->get('session')->getFlashBag()->add('error', 'Je bent niet gemachtigd om dit verzoek te accepteren.');

            return $this->redirectToRoute('dashboard-mentor-requests');
        }

        $dojo = $mentorRequest->getDojo();
        $mentor = $mentorRequest->getUser();

        $dojo->addOwner($mentor);
        $mentor->addDojo($dojo);
        $mentorRequest->setApproved();
        $this->getDoctrine()->getManager()->flush();

        $message = \Swift_Message::newInstance()
            ->setSubject(sprintf('Je hebt nu toegang tot %s', $dojo->getName()))
            ->setFrom('no-reply@coderdojo.nl', 'CoderDojo Nederland')
            ->setReplyTo('contact@coderdojo.nl')
            ->setTo($mentor->getEmail())
            ->setBcc('chris+dojorequest@coderdojo.nl')
            ->setContentType('text/html')
            ->setBody(
                $this->renderView(
                    'CoderDojoWebsiteBundle:Dashboard:request-accepted-mail.html.twig',
                    array(
                        'dojo' => $dojo,
                        'mentor' => $mentor
                    )
                )
            );

        $this->get('mailer')->send($message);

        $this->get('session')->getFlashBag()->add('success', sprintf('We hebben %s aan %s toegevoegd.', $mentor->getFirstName(), $dojo->getName()));

        return $this->redirectToRoute('dashboard-mentor-requests');
    }

    /**
     * @Route("/add-dojo", name="dashboard-add-dojo")
     */
    public function addDojoAction()
    {
        return $this->render('CoderDojoWebsiteBundle:Dashboard:add-dojo.html.twig');
    }
}