<?php

declare(strict_types=1);

namespace CoderDojo\WebsiteBundle\Controller;

use CoderDojo\WebsiteBundle\Entity\Club100;
use CoderDojo\WebsiteBundle\Form\Type\ClubOf100FormType;
use CoderDojo\WebsiteBundle\Repository\Club100Repository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        if ($form->isSubmitted() && $form->isValid()) {
            $member = new Club100();
            $member->setFirstName($form->get('firstName')->getData());
            $member->setLastName($form->get('lastName')->getData());
            $member->setEmail($form->get('email')->getData());
            $member->setReason($form->get('reason')->getData());
            $member->setPublic($form->get('public')->getData() === '1');
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
            $this->sendNotificationEmail($member);

            return $this->redirectToRoute('club_of_100_confirm');
        }

        $repository = $this->get('doctrine')->getRepository(Club100::class);
        $members = $repository->getAllActiveWithImage();
        $keys = count($members) > 3 ? array_rand($members, 3) : array_keys($members);

        $randomMembers = [];
        foreach($keys as $key) {
            $randomMembers[] = $members[$key];
        }

        return $this->render(':Pages:ClubVan100/index.html.twig', ['form' => $form->createView(), 'members' => $randomMembers]);
    }

    /**
     * @Route(name="club_of_100_confirm", path="/bevestigd")
     */
    public function confirmAction(): Response
    {
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
            ->setContentType('text/html')
            ->setBody(
                $this->renderView(':Pages:ClubVan100/Email/welcome.html.twig', ['member' => $member])
            );

        $this->get('mailer')->send($message);
    }

    /**
     * @param Club100 $member
     */
    private function sendNotificationEmail(Club100 $member): void
    {
        /**
         * Send email to dojo contact address
         */
        $message = \Swift_Message::newInstance()
            ->setSubject('Nieuw Club van 100 lid')
            ->setFrom('website@coderdojo.nl', 'CoderDojo Nederland Website')
            ->setReplyTo($member->getEmail())
            ->setTo('contact@coderdojo.nl')
            ->setContentType('text/html')
            ->setBody(
                $this->renderView(':Pages:ClubVan100/Email/notification.html.twig', ['member' => $member])
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
