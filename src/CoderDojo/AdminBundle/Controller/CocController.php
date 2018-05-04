<?php

namespace CoderDojo\AdminBundle\Controller;

use CoderDojo\WebsiteBundle\Command\PrepareCocRequestCommand;
use CoderDojo\WebsiteBundle\Command\ReceiveCocRequestCommand;
use CoderDojo\WebsiteBundle\Entity\CocRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Route("/vog")
 */
class CocController extends Controller
{
    /**
     * @Route("/", name="admin-vog")
     */
    public function indexAction()
    {
        $this->get('session')->getFlashBag()->add('info', 'Het beheren van VOG\'s is nu verhuisd naar het admin paneel!');

        return new RedirectResponse($this->generateUrl('coc_created'));
    }

    /**
     * @Route("/aangevraagd", name="coc_created")
     */
    public function createdAction()
    {
        /** @var CocRequest $cocs */
        $cocs = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:CocRequest')->findBy(
            [
                'status' => 'created'
            ],
            [
                'createdAt' => 'DESC'
            ]);

        return $this->render(
            'AdminBundle:Coc:list.html.twig',
            [
                'title' => 'VOG\'s - Aangevraagd',
                'cocs' => $cocs
            ]
        );
    }

    /**
     * @Route("/voorbereid", name="coc_prepared")
     */
    public function preparedAction()
    {
        /** @var CocRequest $cocs */
        $cocs = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:CocRequest')->findBy(
            [
                'status' => 'prepared'
            ],
            [
                'createdAt' => 'DESC'
            ]);

        return $this->render(
            'AdminBundle:Coc:list.html.twig',
            [
                'title' => 'VOG\'s - Voorbereid',
                'cocs' => $cocs
            ]
        );
    }

    /**
     * @Route("/verzonden", name="coc_requested")
     */
    public function requestedAction()
    {
        /** @var CocRequest $cocs */
        $cocs = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:CocRequest')->findBy(
            [
                'status' => 'requested'
            ],
            [
                'createdAt' => 'DESC'
            ]);

        return $this->render(
            'AdminBundle:Coc:list.html.twig',
            [
                'title' => 'VOG\'s - Verzonden',
                'cocs' => $cocs
            ]
        );
    }

    /**
     * @Route("/voltooid", name="coc_received")
     */
    public function receivedAction()
    {
        /** @var CocRequest $cocs */
        $cocs = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:CocRequest')->findBy(
            [
                'status' => 'received'
            ],
            [
                'createdAt' => 'DESC'
            ]);

        return $this->render(
            'AdminBundle:Coc:list.html.twig',
            [
                'title' => 'VOG\'s - Ontvangen',
                'cocs' => $cocs
            ]
        );
    }

    /**
     * @Route("/{id}/voorbereid", name="admin-vog-prepared")
     *
     * @param CocRequest $cocRequest
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function vogPreparedAction(CocRequest $cocRequest)
    {
        if (null !== $cocRequest->getPreparedAt()) {
            $this->get('session')->getFlashBag()->add('error', 'Dit vog is al klaar gezet!');

            return $this->redirectToRoute('coc_created');
        }

        $command = new PrepareCocRequestCommand($cocRequest->getId());
        $this->get('command_bus')->handle($command);

        $this->get('session')->getFlashBag()->add(
            'success',
            sprintf('Het VOG voor %s is klaar gezet!', $cocRequest->getLetters().' '.$cocRequest->getName())
        );

        return $this->redirectToRoute('coc_created');
    }

    /**
     * @Route("/{id}/ontvangen", name="admin-vog-received")
     *
     * @param CocRequest $cocRequest
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function vogReceivedAction(CocRequest $cocRequest)
    {
        if (null !== $cocRequest->getReceivedAt()) {
            $this->get('session')->getFlashBag()->add('error', 'Dit vog is al ontvangen!');

            return $this->redirectToRoute('coc_requested');
        }

        $command = new ReceiveCocRequestCommand($cocRequest->getId());
        $this->get('command_bus')->handle($command);

        $this->get('session')->getFlashBag()->add(
            'success',
            sprintf('Het VOG voor %s is voltooid!', $cocRequest->getLetters().' '.$cocRequest->getName())
        );

        return $this->redirectToRoute('coc_requested');
    }
}
