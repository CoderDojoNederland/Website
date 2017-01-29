<?php

namespace CoderDojo\WebsiteBundle\Controller;

use CoderDojo\WebsiteBundle\Command\ShipCocRequestCommand;
use CoderDojo\WebsiteBundle\Entity\CocRequest;
use CoderDojo\WebsiteBundle\Form\Type\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function homeAction()
    {
        $dojos = $this->getUpComingDojos();

        return $this->render('CoderDojoWebsiteBundle:Pages:index.html.twig', [
            'dojos' => $dojos
        ]);
    }

    /**
     * @Route("/slack-community", name="slack")
     */
    public function slackAction()
    {
        return $this->render('CoderDojoWebsiteBundle:Pages:slack.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(ContactFormType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $message = \Swift_Message::newInstance()
                    ->setSubject($form->get('subject')->getData())
                    ->setFrom('no-reply@coderdojo.nl', $form->get('naam')->getData())
                    ->setReplyTo($form->get('email')->getData())
                    ->setTo($form->get('ontvanger')->getData())
                    ->setBcc('chris+websiteform@coderdojo.nl')
                    ->setContentType('text/html')
                    ->setBody(
                        $this->renderView(
                            'CoderDojoWebsiteBundle::contactmail.html.twig',
                            array(
                                'naam' => $form->get('naam')->getData(),
                                'email' => $form->get('email')->getData(),
                                'message' => $form->get('message')->getData()
                            )
                        )
                    );

                $this->get('mailer')->send($message);

                $this->get('session')->getFlashBag()->add('success', 'Bedankt voor je bericht!');

                return $this->redirect($this->generateUrl('contact'));
            }
        }

        return $this->render('CoderDojoWebsiteBundle:Pages:contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/vog/{id}/aangevraagd", name="vog-requested")
     */
    public function cocRequestedAction(CocRequest $cocRequest)
    {
        if (null !== $cocRequest->getRequestedAt()) {
            $this->get('session')->getFlashBag()->add('error', 'Dit VOG heb je al aangevraagd.');

            return $this->redirectToRoute('home');
        }

        $command = new ShipCocRequestCommand($cocRequest->getId());
        $this->get('command_bus')->handle($command);

        $this->get('session')->getFlashBag()->add('success', 'Bedankt! We hebben jouw aanvraag genoteerd. Vergeet je niet om het VOG naar ons op te sturen?');

        return $this->redirectToRoute('home');
    }

    /**
     * @return array
     */
    private function getUpComingDojos()
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository("CoderDojoWebsiteBundle:DojoEvent");

        $today = new \DateTime("now");
        $today = $today->format('Y-m-d');

        $query = $repo->createQueryBuilder('d')
            ->where('d.date >= :today')
            ->setParameter('today', $today)
            ->orderBy('d.date', 'ASC')
            ->getQuery()
            ->setMaxResults(15);

        $nextDojos = $query->getResult();

        return $nextDojos;
    }
}
