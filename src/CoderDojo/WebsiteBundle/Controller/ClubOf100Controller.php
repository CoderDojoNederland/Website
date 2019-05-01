<?php

declare(strict_types=1);

namespace CoderDojo\WebsiteBundle\Controller;

use CoderDojo\WebsiteBundle\Entity\Club100;
use CoderDojo\WebsiteBundle\Form\Type\ClubOf100FormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            $member->setInterval($form->get('subscription')->getData());

            $this->get('doctrine')->getManager()->persist($member);
            $this->get('doctrine')->getManager()->flush();
        }

        return $this->render(':Pages:ClubVan100/index.html.twig', ['form' => $form->createView()]);
    }
}
