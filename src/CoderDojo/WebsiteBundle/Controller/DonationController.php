<?php

declare(strict_types=1);

namespace CoderDojo\WebsiteBundle\Controller;

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

            $mollie = new MollieApiClient();
            $mollie->setApiKey($this->getParameter('mollie_key'));
            $molliePayment = $mollie->payments->create(
                [
                    'amount' => [
                        'currency' => 'EUR',
                        'value' => ''.number_format($amount, 2)
                    ],
                    'description' => 'Donatie aan CoderDojo Nederland',
                    'redirectUrl' => $this->generateUrl('donate', ['thanks' => 'true'],UrlGeneratorInterface::ABSOLUTE_URL),
                    'locale' => 'nl_NL'
                ]
            );
            return $this->redirect($molliePayment->getCheckoutUrl());
        }

        if ($request->query->has('thanks')) {
            $this->get('session')->getFlashBag()->add('success', 'Bedankt voor je donatie!');
        }

        return $this->render(
            ':Pages:donate.html.twig'
        );
    }
}
