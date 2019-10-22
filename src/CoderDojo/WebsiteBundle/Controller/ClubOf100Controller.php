<?php

declare(strict_types=1);

namespace CoderDojo\WebsiteBundle\Controller;

use CoderDojo\WebsiteBundle\Entity\Club100;
use CoderDojo\WebsiteBundle\Entity\Donation;
use CoderDojo\WebsiteBundle\Entity\Payment;
use CoderDojo\WebsiteBundle\Form\Type\ClubOf100FormType;
use CoderDojo\WebsiteBundle\Repository\Club100Repository;
use CoderDojo\WebsiteBundle\Service\NextDonationFinder;
use Mollie\Api\MollieApiClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

            $ecurring        = $this->get('coder_dojo.website_bundle.ecurring');
            $customerId      = $ecurring->createCustomer($member);
            $confirmationUrl = $ecurring->createSubscription($customerId, $member->getInterval(), $member->getHash());
            $member->setConfirmationUrl($confirmationUrl);

            $this->get('doctrine')->getManager()->persist($member);
            $this->get('doctrine')->getManager()->flush();

            return new RedirectResponse($confirmationUrl);
        }

        $repository = $this->get('doctrine')->getRepository(Club100::class);
        $members = $repository->getAllActiveWithImage();
        $keys = count($members) > 0 ? array_rand($members, 3) : [];

        $randomMembers = [];
        foreach($keys as $key) {
            $randomMembers[] = $members[$key];
        }

        return $this->render(':Pages:ClubVan100/index.html.twig', ['form' => $form->createView(), 'members' => $randomMembers]);
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

        if ($member->isConfirmed()) {
            return $this->render(':Pages:ClubVan100/confirmed.html.twig', ['new' => false]);
        }

        $member->setConfirmed(true);
        $this->get('doctrine')->getManager()->flush();

        $this->sendWelcomeEmail($member);

        return $this->render(':Pages:ClubVan100/confirmed.html.twig', ['new' => true]);
    }

    /**
     * @Route(name="club_of_100_showcase", path="/leden")
     */
    public function showcaseAction(): Response
    {
        $repository = $this->get('doctrine')->getRepository(Club100::class);
        /** @var Club100[] $members */
        $members = $repository->getAllActive(Club100Repository::PUBLIC_ONLY);

        $total = count($repository->getAllActive());

        return $this->render(':Pages:ClubVan100/members.html.twig', ['members' => $members, 'total' => $total]);
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
