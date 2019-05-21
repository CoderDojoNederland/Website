<?php

declare(strict_types=1);

namespace CoderDojo\WebsiteBundle\Controller;

use CoderDojo\WebsiteBundle\Entity\Club100;
use CoderDojo\WebsiteBundle\Entity\Donation;
use CoderDojo\WebsiteBundle\Entity\Payment;
use CoderDojo\WebsiteBundle\Form\Type\ClubOf100FormType;
use CoderDojo\WebsiteBundle\Service\NextDonationFinder;
use Mollie\Api\MollieApiClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route(path="/club-van-100")
 */
class ClubOf100Controller extends Controller
{
    /**
     * @Route(name="club_of_100")
     */
    public function indexAction(Request $request): Response
    {
        $formFactory = $this->get('form.factory');

        $form = $formFactory->create(ClubOf100FormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $repository = $this->get('doctrine')->getRepository(Club100::class);
            $existing = $repository->findOneBy(['email' => $form->get('email')->getData()]);
            if ($existing) {
                $form->addError(new FormError('Er bestaat al een lid met dit emailadres'));
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $member = new Club100();
            $member->setFirstName($form->get('firstName')->getData());
            $member->setLastName($form->get('lastName')->getData());
            $member->setEmail($form->get('email')->getData());
            $member->setReason($form->get('reason')->getData());
            $member->setPublic($form->get('public')->getData() === '1');
            $member->setInterval($form->get('subscription')->getData());
            $member->setMemberType($form->get('type')->getData());

            if (empty($form->get('twitter')->getData()) === false) {
                $member->setTwitter($form->get('twitter')->getData());
            }

            if (empty($form->get('company')->getData()) === false) {
                $member->setCompany($form->get('company')->getData());
            }

            if (empty($form->get('avatar')->getData()) === false) {
                $avatar = $this->uploadAvatar($form->get('avatar')->getData(), $member);
                $member->setAvatar($avatar);
            }

            $this->get('doctrine')->getManager()->persist($member);
            $this->get('doctrine')->getManager()->flush();

            $this->sendWelcomeEmail($member);

            return $this->redirectToRoute('club_of_100_thanks');
        }

        $repository = $this->get('doctrine')->getRepository(Club100::class);
        $members = $repository->getAllWithImage();
        $keys = array_rand($members, 3);

        return $this->render(':Pages:ClubVan100/index.html.twig', ['form' => $form->createView(), 'members' => [$members[$keys[0]], $members[$keys[1]], $members[$keys[2]]]]);
    }

    /**
     * @Route(name="club_of_100_thanks", path="/bedankt")
     */
    public function thankyouAction(): Response
    {
        return $this->render(':Pages:ClubVan100/bedankt.html.twig');
    }

    /**
     * @Route(name="club_of_100_paid", path="/betaald")
     */
    public function paidAction(): Response
    {
        return $this->render(':Pages:ClubVan100/donatie.html.twig');
    }

    /**
     * @Route(name="club_of_100_confirm", path="/bevestigen/{hash}")
     */
    public function confirmAction(string $hash): Response
    {
        $repository = $this->get('doctrine')->getRepository(Club100::class);
        /** @var Club100 $member */
        $member = $repository->findOneBy(['hash' => $hash]);

        if ($member === null) {
            throw new NotFoundHttpException('No club 100 member with hash '.$hash.' was found');
        }

        $member->setConfirmed(true);
        $this->get('doctrine')->getManager()->flush();

        if (NextDonationFinder::shouldSendFirstRequest($member)) {
            $donation = new Donation($member);
            $this->get('doctrine')->getManager()->persist($donation);
            $this->get('doctrine')->getManager()->flush();
            $this->get('doctrine')->getManager()->refresh($donation);

            /**
             * Send email to dojo contact address
             */
            $message = \Swift_Message::newInstance()
             ->setSubject('Je eerste donatie')
             ->setFrom('contact@coderdojo.nl', 'CoderDojo Nederland')
             ->setTo($member->getEmail())
             ->setBcc('website+club100@coderdojo.nl')
             ->setContentType('text/html')
             ->setBody(
                 $this->renderView(':Pages:ClubVan100/Email/first_donation.html.twig',
                   [
                       'member' => $member,
                       'nextDonation' => NextDonationFinder::findNextDonation($member),
                       'donation' => $donation
                   ]
                 )
             );

            $this->get('mailer')->send($message);
        }

        return $this->render(':Pages:ClubVan100/confirmed.html.twig');
    }

    /**
     * @Route(name="club_of_100_showcase", path="/leden")
     */
    public function showcaseAction(): Response
    {
        $repository = $this->get('doctrine')->getRepository(Club100::class);
        /** @var Club100[] $members */
        $members = $repository->findBy(['confirmed' => true, 'public' => true]);

        $total = count($repository->findBy(['confirmed' => true]));

        return $this->render(':Pages:ClubVan100/members.html.twig', ['members' => $members, 'total' => $total]);
    }

    /**
     * @Route(name="club_of_100_payment", path="/donatie/{uuid}")
     */
    public function createPaymentAction(string $uuid): Response
    {
        $repository = $this->get('doctrine')->getRepository(Donation::class);
        /** @var Donation $donation */
        $donation = $repository->findOneBy(['uuid' => $uuid]);

        $mollie = new MollieApiClient();
        $mollie->setApiKey($this->getParameter('mollie_key'));

        $interval = $donation->getMember()->getInterval();

        switch($interval){
            case Club100::INTERVAL_YEARLY:
                $description = 'Jaarlijkse donatie aan CoderDojo Nederland.';
                $value = '100.00';
                break;
            case Club100::INTERVAL_SEMI_YEARLY:
                $description = 'Halfjaarlijkse donatie aan CoderDojo Nederland.';
                $value = '50.00';
                break;
            case Club100::INTERVAL_QUARTERLY:
                $description = 'Kwartaallijkse donatie aan CoderDojo Nederland.';
                $value = '25.00';
                break;
            default:
                throw new \Exception('Unknown interval');
        }

        if ($this->getParameter('kernel.environment') === 'prod') {
            $webhook = $this->generateUrl('club_of_100_webhook', ['uuid' => $uuid], UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $webhook = 'https://e6de8ac3.ngrok.io/club-van-100/donatie/'.$uuid.'/webhook';
        }

        $molliePayment = $mollie->payments->create(
            [
                'amount' => [
                    'currency' => 'EUR',
                    'value' => $value
                ],
                'description' => $description,
                'redirectUrl' => $this->generateUrl('club_of_100_paid', [],UrlGeneratorInterface::ABSOLUTE_URL),
                'webhookUrl' => $webhook,
                'locale' => 'nl_NL'
            ]
        );

        $payment = new Payment($donation, $molliePayment->id, $molliePayment->status, $molliePayment->getCheckoutUrl());
        $this->get('doctrine')->getManager()->persist($payment);
        $this->get('doctrine')->getManager()->flush();

        return $this->redirect($payment->getCheckoutUrl());
    }

    /**
     * @Route(name="club_of_100_webhook", path="/donatie/{uuid}/webhook")
     */
    public function updatePaymentAction(Request $request, string $uuid): Response
    {
        $molliePaymentId = $request->request->get('id');
        $repository = $this->get('doctrine')->getRepository(Donation::class);
        $paymentRepository = $this->get('doctrine')->getRepository(Payment::class);
        /** @var Payment $payment */
        $payment = $paymentRepository->findOneBy(['mollieId' => $molliePaymentId]);
        /** @var Donation $donation */
        $donation = $repository->findOneBy(['uuid' => $uuid]);

        $mollie = new MollieApiClient();
        $mollie->setApiKey($this->getParameter('mollie_key'));

        $molliePayment = $mollie->payments->get($molliePaymentId);

        if ($molliePayment->status === $payment->getStatus()) {
            return new Response('ok');
        }

        $payment->setStatus($molliePayment->status);

        if ($payment->getStatus() === 'paid') {
            $donation->setPayment($payment);

            $this->sendSuccessMail($donation);
        }

        $this->get('doctrine')->getManager()->flush();

        return new Response('ok');
    }

    /**
     * @param Club100 $member
     */
    private function sendWelcomeEmail(Club100 $member): void
    {
        /**
         * Send email to dojo contact address
         */
        $message = \Swift_Message::newInstance()
            ->setSubject('Welkom bij de Club van 100')
            ->setFrom('contact@coderdojo.nl', 'CoderDojo Nederland')
            ->setTo($member->getEmail())
            ->setBcc('website+club100@coderdojo.nl')
            ->setContentType('text/html')
            ->setBody(
                $this->renderView(':Pages:ClubVan100/Email/welcome.html.twig', ['member' => $member])
            );

        $this->get('mailer')->send($message);
    }

    /**
     * @param Donation $donation
     */
    private function sendSuccessMail(Donation $donation): void
    {
        /**
         * Send email to dojo contact address
         */
        $message = \Swift_Message::newInstance()
         ->setSubject('Donatie geslaagd')
         ->setFrom('contact@coderdojo.nl', 'CoderDojo Nederland')
         ->setTo($donation->getMember()->getEmail())
         ->setBcc('website+club100@coderdojo.nl')
         ->setContentType('text/html')
         ->setBody(
             $this->renderView(':Pages:ClubVan100/Email/payment_success.html.twig', ['member' => $donation->getMember()])
         );

        $this->get('mailer')->send($message);
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param Club100      $member
     *
     * @return string
     */
    private function uploadAvatar(UploadedFile $uploadedFile, Club100 $member): string
    {
        $kernel = $this->get('kernel')->getRootDir();
        $destination = $kernel . '/../web/club-100-avatars';

        $filesystem = new Filesystem();

        if(!$filesystem->exists($destination)) {
            $filesystem->mkdir($destination);
        }

        $filename = sprintf(
            '%s_%s_%d.%s',
            $member->getFirstName(),
            $member->getLastName(),
            time(),
            $uploadedFile->getClientOriginalExtension()
        );
        $filename = str_replace(' ', '', $filename);
        $filename = strtolower($filename);

        $uploadedFile->move($destination, $filename);

        return $filename;
    }
}
