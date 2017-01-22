<?php

namespace CoderDojo\WebsiteBundle\Controller;

use CoderDojo\WebsiteBundle\Command\CreateCocRequestCommand;
use CoderDojo\WebsiteBundle\Entity\Claim;
use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Entity\DojoRequest;
use CoderDojo\WebsiteBundle\Entity\User;
use CoderDojo\WebsiteBundle\Form\Type\CocRequestFormType;
use CoderDojo\WebsiteBundle\Form\Type\EventFormType;
use Ramsey\Uuid\Uuid;
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
        if (
            true === empty($this->getUser()->getFirstName()) ||
            true === empty($this->getUser()->getLastName()) ||
            true === empty($this->getUser()->getPhone())
        ) {
            $text = "Welkom bij de nieuwe coderdojo.nl! Omdat we de dojo's nu los hebben getrokken van de profielen vragen we je om jouw profiel bij te werken. Vul onderstaande gegevens in om verder te gaan!";

            $this->get('session')->getFlashBag()->add('success', $text);

            return $this->redirectToRoute('fos_user_profile_edit');
        }

        return $this->render('CoderDojoWebsiteBundle:Dashboard:Pages/dashboard.html.twig');
    }

    /**
     * @Route("/claim-dojo/{dojoId}/{hash}", name="dashboard-claim-dojo-verify")
     */
    public function verifyClaimDojoAction($dojoId, $hash)
    {
        $dojo = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:Dojo')->find($dojoId);

        if (null === $dojo) {
            $this->get('session')->getFlashBag()->add('error', 'De dojo waar je toegang toe wilt kan niet gevonden worden.');

            return $this->redirectToRoute('dashboard-add-dojo');
        }

        $claim = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:Claim')->findOneBy([
            'dojo' => $dojo,
            'user' => $this->getUser(),
        ]);

        /**
         * Check if the claim actually exists
         */
        if (null === $claim) {
            $this->get('session')->getFlashBag()->add('error', 'We konden jouw claim niet vinden.');

            return $this->redirectToRoute('dashboard-add-dojo');
        }

        /**
         * Check is the claim is not expired
         */
        if (true === $claim->isExpired()) {
            $this->getDoctrine()->getManager()->remove($claim);
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('error', 'Deze claim is reeds verlopen! Je moet binnen 48h bevestigen.');

            return $this->redirectToRoute('dashboard-add-dojo');
        }

        /**
         * Check if the claim has already been verified
         */
        if (null !== $claim->getClaimedAt()) {
            $this->get('session')->getFlashBag()->add('success', 'Deze claim heb je al bevestigd');

            return $this->redirectToRoute('dashboard-add-dojo');
        }

        /**
         * Check if the hash is correct
         */
        if ($hash !== $claim->getHash()) {
            $this->get('session')->getFlashBag()->add('error', 'De link voor deze claim is niet geldig, zorg dat je met dezelfde account bent inglogged als waarmee je de claim hebt aangevraagd.');

            return $this->redirectToRoute('dashboard-add-dojo');
        }

        /**
         * Claim the dojo
         */
        $claim->claim();
        $dojo->addOwner($this->getUser());
        $this->getUser()->addDojo($dojo);
        $this->getDoctrine()->getManager()->flush();

        $claims = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:Claim')->findBy([
            'dojo' => $dojo,
            'claimed' => null
        ]);

        /**
         * Convert other claims to mentor requests
         */
        foreach ($claims as $claim) {
            $mentorRequest = new DojoRequest($dojo, $claim->getUser());
            $this->getDoctrine()->getManager()->persist($mentorRequest);
            $this->getDoctrine()->getManager()->remove($claim);
            $this->getDoctrine()->getManager()->flush();
        }

        $this->get('session')->getFlashBag()->add('success', 'Je hebt de dojo geclaimed!');

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/claim-dojo/{dojoId}", name="dashboard-claim-dojo")
     */
    public function claimDojoAction($dojoId)
    {
        $dojo = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:Dojo')->find($dojoId);

        if (null === $dojo) {
            $this->get('session')->getFlashBag()->add('error', 'De dojo waar je toegang toe wilt kan niet gevonden worden.');

            return $this->redirectToRoute('dashboard-add-dojo');
        }

        /** @var User $user */
        $user = $this->getUser();

        if (true === in_array($dojo, $user->getDojos()->toArray())) {
            $this->get('session')->getFlashBag()->add('error', 'Je bent al verbonden aan deze dojo!');

            return $this->redirectToRoute('dashboard-add-dojo');
        }

        $claim = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:Claim')->findOneBy([
            'dojo' => $dojo,
            'user' => $this->getUser(),
        ]);

        if (null !== $claim) {
            /**
             * Has this claim already been handled?
             */
            if (null !== $claim->getClaimedAt()) {
                $this->get('session')->getFlashBag()->add('error', 'Dit verzoek is al geclaimed!');

                return $this->redirectToRoute('dashboard-add-dojo');
            }

            /**
             * Is this claim still valid?
             */
            if ($claim->isExpired()) {
                $this->get('session')->getFlashBag()->add('error', 'Dit verzoek is verlopen en verwijderd. Je dient een claim binnen 24h te bevestigen via de link in onze email.');

                $this->getDoctrine()->getManager()->remove($claim);
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('dashboard-add-dojo');
            }

            $this->get('session')->getFlashBag()->add('success', 'We hebben al een claim voor je klaar staan. Controleer het dojo emailadres van de dojo om de claim te bevestigen.');
            return $this->redirectToRoute('dashboard-add-dojo');
        }

        $claim = new Claim($dojo, $this->getUser());
        $this->getDoctrine()->getManager()->persist($claim);
        $this->getDoctrine()->getManager()->flush();

        /**
         * Send email to dojo contact address
         */
        $message = \Swift_Message::newInstance()
            ->setSubject(sprintf('%s wilt %s claimen', $this->getUser()->getFirstname(), $dojo->getName()))
            ->setFrom('contact@coderdojo.nl', 'CoderDojo Nederland')
            ->setTo($dojo->getEmail())
            ->setBcc('chris+dojorequest@coderdojo.nl')
            ->setContentType('text/html')
            ->setBody(
                $this->renderView(
                    'CoderDojoWebsiteBundle:Dashboard:Email/claimMail.html.twig',
                    array(
                        'dojo' => $dojo,
                        'user' => $this->getUser(),
                        'claim' => $claim
                    )
                )
            );

        $this->get('mailer')->send($message);

        $this->get('session')->getFlashBag()->add('success', 'We hebben een email gestuurd naar de dojo. Klik op de link in de mail om jouw claim te bevestigen.');

        return $this->redirectToRoute('dashboard-add-dojo');
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

        /** @var User $user */
        $user = $this->getUser();

        if (true === in_array($dojo, $user->getDojos()->toArray())) {
            $this->get('session')->getFlashBag()->add('error', 'Je bent al verbonden aan deze dojo!');

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
            ->setFrom('contact@coderdojo.nl', sprintf('%s %s', $this->getUser()->getFirstname(), $this->getUser()->getLastname()))
            ->setReplyTo($this->getUser()->getEmail())
            ->setTo($dojo->getEmail())
            ->setBcc('chris+dojorequest@coderdojo.nl')
            ->setContentType('text/html')
            ->setBody(
                $this->renderView(
                    'CoderDojoWebsiteBundle:Dashboard:Email/requestMail.html.twig',
                    array(
                        'dojo' => $dojo,
                        'user' => $this->getUser(),
                        'requestId' => $dojoRequest->getId()
                    )
                )
            );

        $this->get('mailer')->send($message);

        $this->get('session')->getFlashBag()->add('success', 'We hebben een email gestuurd naar deze dojo met het verzoek om jou toegang te geven.');

        return $this->redirectToRoute('dashboard');
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

        return $this->render('CoderDojoWebsiteBundle:Dashboard:Pages/mentor-requests.html.twig', ['requests' => $requests]);
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
                    'CoderDojoWebsiteBundle:Dashboard:Email/request-accepted-mail.html.twig',
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
        $dojos = $this->get('doctrine')->getRepository('CoderDojoWebsiteBundle:Dojo')->findBy([],['city'=>'ASC']);

        return $this->render(
            'CoderDojoWebsiteBundle:Dashboard:Pages/add-dojo.html.twig',
            [
                'dojos'=>$dojos
            ]
        );
    }

    /**
     * @Route("/events/{id}", name="dashboard-dojo-events")
     */
    public function eventAction($id)
    {
        $dojo = $this->getDoctrine()->getRepository("CoderDojoWebsiteBundle:Dojo")->find($id);
        $events = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:DojoEvent')->findBy(
            [
            "dojo" => $dojo
            ],
            [
                "date" => 'DESC'
            ]
        );

        return $this->render(
            'CoderDojoWebsiteBundle:Dashboard/Pages:events.html.twig',
            [
                'dojo' => $dojo,
                'events' => $events
            ]
        );
    }

    /**
     * @Route("/events/{id}/add", name="dashboard-dojo-events-add")
     */
    public function addEventAction(Request $request, $id)
    {
        /** @var User $user */
        $user = $this->getUser();
        $dojo = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:Dojo')->find($id);

        if (false === $dojo->isOwner($user)) {
            $this->get('session')->getFlashBag()->add('error', 'Zo te zien heb je geen rechten om aan deze dojo een event toe te voegen.');
            return $this->redirectToRoute('dashboard');
        }

        $form = $this->createForm(EventFormType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $event = new DojoEvent();
                $event->setName($form->get('name')->getData());
                $event->setDate($form->get('date')->getData());
                $event->setUrl($form->get('url')->getData());
                $event->setType(DojoEvent::TYPE_CUSTOM);
                $event->setDojo($dojo);

                $dojo->addEvent($event);

                $this->getDoctrine()->getManager()->persist($event);
                $this->getDoctrine()->getManager()->flush();

                $this->get('session')->getFlashBag()->add('success', 'Dit event is toegevoegd!');

                return $this->redirectToRoute('dashboard-dojo-events', ['id'=>$dojo->getId()]);
            } else {
                return $this->render('CoderDojoWebsiteBundle:Dashboard:Pages/events-add.html.twig', [
                    'form'=>$form->createView(),
                    'dojo' => $dojo
                ]);
            }
        }

        return $this->render('CoderDojoWebsiteBundle:Dashboard:Pages/events-add.html.twig', [
            'form'=>$form->createView(),
            'dojo' => $dojo
        ]);
    }

    /**
     * @Route("/events/{id}/edit", name="dashboard-dojo-events-edit")
     */
    public function editEventAction(Request $request, $id)
    {
        $event = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:DojoEvent')->find($id);
        $dojo = $event->getDojo();
        $user = $this->getUser();

        if (false === $dojo->isOwner($user)) {
            $this->get('session')->getFlashBag()->add('error', 'Zo te zien heb je geen rechten om voor deze dojo events te bewerken.');
            return $this->redirectToRoute('dashboard');
        }

        if (DojoEvent::TYPE_CUSTOM !== $event->getType()) {
            $this->get('session')->getFlashBag()->add('error', 'Dit event kan alleen op zen.coderdojo.com bewerkt worden.');
            return $this->redirectToRoute('dashboard-dojo-events');
        }

        $form = $this->createForm(EventFormType::class, $event);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $event->setName($form->get('name')->getData());
                $event->setDate($form->get('date')->getData());
                $event->setUrl($form->get('url')->getData());
                $event->setType(DojoEvent::TYPE_CUSTOM);

                $this->getDoctrine()->getManager()->flush();

                $this->get('session')->getFlashBag()->add('success', 'Dit event is bewerkt!');

                return $this->redirectToRoute('dashboard-dojo-events', ['id'=>$dojo->getId()]);
            } else {
                return $this->render('CoderDojoWebsiteBundle:Dashboard:Pages/events-add.html.twig', [
                    'form'=>$form->createView(),
                    'dojo' => $dojo
                ]);
            }
        }

        return $this->render('CoderDojoWebsiteBundle:Dashboard:Pages/events-add.html.twig', [
            'form'=>$form->createView(),
            'dojo' => $dojo
        ]);
    }

    /**
     * @Route("/dojo/{id}/event/remove/{eventId}", name="dashboard-dojo-event-remove")
     */
    public function removeEventAction(Request $request, $id, $eventId)
    {
        $dojo = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:Dojo')->find($id);
        $event = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:DojoEvent')->find($eventId);

        if (false === $dojo->isOwner($this->getUser())) {
            $this->get('session')->getFlashBag()->add('error', 'Zo te zien heb je geen rechten om voor deze dojo events te verwijderen.');
            return $this->redirectToRoute('dashboard');
        }

        if ($event->getDojo() !== $dojo) {
            $this->get('session')->getFlashBag()->add('error', 'Dit event hoor niet bij deze dojo.');
            return $this->redirectToRoute('dashboard');
        }

        if (true === $request->query->has('confirmed')) {
            $this->getDoctrine()->getManager()->remove($event);
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Event is verwijderd!');

            return $this->redirectToRoute('dashboard-dojo-events', ['id'=>$dojo->getId()]);
        }

        return $this->render('CoderDojoWebsiteBundle:Dashboard:Pages/event-remove.html.twig', [
            'dojo' => $dojo,
            'event' => $event
        ]);

    }

    /**
     * @Route("/mentors/{id}", name="dashboard-dojo-mentors")
     */
    public function mentorAction($id)
    {
        $dojo = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:Dojo')->find($id);

        if (false === $dojo->isOwner($this->getUser())) {
            $this->get('session')->getFlashBag()->add('error', 'Zo te zien heb je geen rechten om voor deze dojo mentoren te beheren.');
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('CoderDojoWebsiteBundle:Dashboard:Pages/mentors.html.twig', [
            'dojo' => $dojo
        ]);
    }

    /**
     * @Route("/dojo/{id}/mentor/remove/{mentorId}", name="dashboard-dojo-mentors-remove")
     */
    public function removeMentorAction(Request $request, $id, $mentorId)
    {
        $dojo = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:Dojo')->find($id);
        $mentor = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:User')->find($mentorId);

        if (false === $dojo->isOwner($this->getUser())) {
            $this->get('session')->getFlashBag()->add('error', 'Zo te zien heb je geen rechten om voor deze dojo mentoren te beheren.');

            return $this->redirectToRoute('dashboard');
        }

        if ($mentor === $this->getUser()) {
            $this->get('session')->getFlashBag()->add('error', 'Je kunt jezelf niet verwijderen!');

            return $this->redirectToRoute('dashboard-dojo-mentors', ['id'=>$dojo->getId()]);
        }

        if (true === $request->query->has('confirmed')) {
            $mentor->removeDojo($dojo);
            $dojo->removeOwner($mentor);
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Mentor is verwijderd!');

            return $this->redirectToRoute('dashboard-dojo-mentors', ['id'=>$dojo->getId()]);
        }

        return $this->render('CoderDojoWebsiteBundle:Dashboard:Pages/mentors-remove.html.twig', [
            'dojo' => $dojo,
            'mentor' => $mentor
        ]);
    }

    /**
     * @Route("/vog", name="dashboard-vog")
     */
    public function vogAction(Request $request)
    {
        $form = $this->createForm(CocRequestFormType::class);
        $form->handleRequest($request);

        if ('POST' === $request->getMethod() && $form->isValid())
        {
            $id = Uuid::uuid4()->toString();
            $data = $form->getData();

            $command = new CreateCocRequestCommand(
                $id,
                $data['letters'],
                $data['name'],
                $data['birthdate'],
                $data['email'],
                $data['notes'],
                $this->getUser()->getId(),
                $data['dojo']
            );

            $this->get('command_bus')->handle($command);

            $this->get('session')->getFlashBag()->add('success', 'Bedankt! We gaan zo snel mogelijk aan de slag om dit VOG aan te vragen.');
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('CoderDojoWebsiteBundle:Dashboard:Pages/vog.html.twig', ['form'=>$form->createView()]);
    }

    /**
     * @return Response
     */
    public function countDojoRequestsAction()
    {
        $requests = 0;

        /** @var Dojo $dojo */
        foreach ($this->getUser()->getDojos() as $dojo) {
            $mentorRequests = $dojo->getMentorRequests();

            foreach ($mentorRequests as $mentorRequest) {
                if (null === $mentorRequest->getApproved()) {
                    $requests++;
                }
            }
        }

        return new Response($requests);
    }
}