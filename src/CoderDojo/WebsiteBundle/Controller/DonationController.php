<?php

declare(strict_types=1);

namespace CoderDojo\WebsiteBundle\Controller;

use CoderDojo\WebsiteBundle\Form\Type\DonationFormType;
use GuzzleHttp\Client;
use Mollie\Api\MollieApiClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DonationController extends Controller
{
    /**
     * @Route("/doneren", name="donate")
     */
    public function donateAction(Request $request)
    {
        if ($request->query->has('amount')) {
            $amount = (int) $request->query->get('amount');

            if ($amount <= 5) {
                $this->get('session')->getFlashBag()->add('danger', 'Een donatie moet minimaal 5 euro zijn.');
                return $this->redirectToRoute('donate');
            }

            return $this->redirectToRoute('donate_confirm', ['amount' => $amount]);
        }

        if ($request->query->has('thanks')) {
            $this->get('session')->getFlashBag()->add('success', 'Bedankt voor je donatie!');
        }

        return $this->render(
            ':Pages:donate.html.twig'
        );
    }

    /**
     * @Route("/doneren/bevestigen", name="donate_confirm")
     */
    public function confirmAction(Request $request)
    {
        $reCaptchaSiteKey = $this->container->getParameter('recaptcha_site');
        $captcha = $request->get('g-recaptcha-response');

        $form = $this->createForm(DonationFormType::class);
        $form->handleRequest($request);

        if (false === $request->query->has('amount') && $form->get('amount')->isEmpty()) {
            return $this->redirectToRoute('donate');
        }

        if ($request->query->has('amount')) {
            $amount = (int) $request->query->get('amount');

            if ($amount <= 5) {
                $this->get('session')->getFlashBag()->add('danger', 'Een donatie moet minimaal 5 euro zijn.');
                return $this->redirectToRoute('donate');
            }

            $form->get('amount')->setData($amount);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if($captcha === null || false === $this->isCaptchaValid($captcha)) {
                $this->get('session')->getFlashBag()->add('danger', 'De captcha validatie is mislukt!');
                return $this->redirectToRoute('donate');
            }

            $mollie = new MollieApiClient();
            $mollie->setApiKey($this->getParameter('mollie_key'));
            $donationAmount          = (int)$form->get('amount')->getData();
            $molliePayment = $mollie->payments->create(
                [
                    'amount' => [
                        'currency' => 'EUR',
                        'value' => ''.number_format($donationAmount, 2)
                    ],
                    'description' => 'Donatie aan CoderDojo Nederland',
                    'redirectUrl' => $this->generateUrl('donate', ['thanks' => 'true'],UrlGeneratorInterface::ABSOLUTE_URL),
                    'locale' => 'nl_NL'
                ]
            );

            $message = \Swift_Message::newInstance()
                ->setSubject('Donatie via coderdojo.nl')
                ->setFrom('contact@coderdojo.nl', 'CoderDojo.nl Donaties')
                ->setTo('chris@coderdojo.nl')
                ->setContentType('text/html')
                ->setBody(
                    'Er komt een donatie van &euro; '.number_format($donationAmount, 0, ',', '.').' aan!'.PHP_EOL
                    .'Het commentaar is als volgt:'.PHP_EOL.$form->get('comment')->getData()
                );

            $this->get('mailer')->send($message);

            return $this->redirect($molliePayment->getCheckoutUrl());
        }

        return $this->render(':Pages:donate_confirm.html.twig', ['form' => $form->createView(), 'amount' => $amount, 'recaptcha_site_key' => $reCaptchaSiteKey]);
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
}
