<?php

namespace Coderdojo\WebsiteBundle\Controller;

use Coderdojo\WebsiteBundle\Form\Type\ContactFormType;
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

        return $this->render('CoderdojoWebsiteBundle:Pages:index.html.twig', [
            'dojos' => $dojos
        ]);
    }

    /**
     * @Route("/slack-community", name="slack")
     */
    public function slackAction()
    {
        return $this->render('CoderdojoWebsiteBundle:Pages:slack.html.twig');
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
            ->getQuery()
            ->setMaxResults(15);

        $nextDojos = $query->getResult();

        return $nextDojos;
    }
}
