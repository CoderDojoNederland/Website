<?php

namespace CoderDojo\WebsiteBundle\Controller;

use CoderDojo\WebsiteBundle\Command\ShipCocRequestCommand;
use CoderDojo\WebsiteBundle\Entity\CocRequest;
use CoderDojo\WebsiteBundle\Entity\User;
use CoderDojo\WebsiteBundle\Form\Type\ContactFormType;
use GuzzleHttp\Client;
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
        $articles = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:Article')->getLatest(4);

        return $this->render(':Pages:index.html.twig', [
            'dojos' => $dojos,
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/slack-community", name="slack")
     */
    public function slackAction()
    {
        return $this->render(':Pages:slack.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(ContactFormType::class);
        $reCaptchaSiteKey = $this->container->getParameter('recaptcha_site');

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $captcha = $request->get('g-recaptcha-response');
                if($captcha === null || false === $this->isCaptchaValid($captcha)) {
                    $this->get('session')->getFlashBag()->add('danger', 'De captcha validatie is mislukt!');

                    return $this->render(':Pages:contact.html.twig', array(
                        'form' => $form->createView(),
                        'recaptcha_site_key' => $reCaptchaSiteKey
                    ));
                }

                $message = \Swift_Message::newInstance()
                    ->setSubject($form->get('subject')->getData())
                    ->setFrom('contact@coderdojo.nl', $form->get('naam')->getData())
                    ->setReplyTo($form->get('email')->getData())
                    ->setTo($form->get('ontvanger')->getData())
                    ->setBcc('website+websiteform@coderdojo.nl')
                    ->setContentType('text/html')
                    ->setBody(
                        $this->renderView(
                            '::contactmail.html.twig',
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

        return $this->render(':Pages:contact.html.twig', array(
            'form' => $form->createView(),
            'recaptcha_site_key' => $reCaptchaSiteKey
        ));
    }

    private function isCaptchaValid(string $value): bool
    {
        $client = new Client();
        $response = $client->request(
            'POST',
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params' => [
                    'secret' => $this->container->getParameter('recaptcha_secret'),
                    'response' => $value
                ]
            ]
        );

        $result = json_decode($response->getBody()->getContents());

        return($result->success && $response->getStatusCode() === 200);
    }

    /**
     * @Route("/vog/{id}/aangevraagd", name="vog-requested")
     */
    public function cocRequestedAction(CocRequest $cocRequest)
    {
        if (null !== $cocRequest->getRequestedAt()) {
            $this->get('session')->getFlashBag()->add('error', 'Dit VOG heb je al gemarkeerd als aangevraagd.');

            return $this->redirectToRoute('home');
        }

        $command = new ShipCocRequestCommand($cocRequest->getId());
        $this->get('command_bus')->handle($command);

        $this->get('session')->getFlashBag()->add('success', 'Bedankt! We hebben jouw aanvraag genoteerd. Vergeet niet om het originele VOG per post naar ons door te sturen!');

        $user = $this->getUser();
        if ($user && $user->hasRole('ROLE_ADMIN')) {
            return $this->redirectToRoute('coc_requested');
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/style", name="style-guide")
     */
    public function styleAction()
    {
        return $this->redirect('https://app.frontify.com/d/U9lF61SDuNiZ/coderdojo-nederland-styleguide');
    }

    /**
     * @return array
     */
    private function getUpComingDojos()
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository("CoderDojoWebsiteBundle:DojoEvent");

        $nextDojos = $repo->getAllUpcomingEvents(15);

        return $nextDojos;
    }
}
