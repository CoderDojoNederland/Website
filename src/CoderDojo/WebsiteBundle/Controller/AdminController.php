<?php

namespace CoderDojo\WebsiteBundle\Controller;

use CoderDojo\WebsiteBundle\Command\PrepareCocRequestCommand;
use CoderDojo\WebsiteBundle\Command\ReceiveCocRequestCommand;
use CoderDojo\WebsiteBundle\Entity\CocRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/vog", name="admin-vog")
     */
    public function listVogAction()
    {
        /** @var CocRequest[] $cocs */
        $cocs = $this->getDoctrine()->getRepository('CoderDojoWebsiteBundle:CocRequest')->findAll();

        return $this->render('CoderDojoWebsiteBundle:Admin:list_vog.html.twig', [
            'cocs' => $cocs
        ]);
    }

    /**
     * @Route("/vog/{id}/prepared", name="admin-vog-prepared")
     *
     * @param CocRequest $cocRequest
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function vogPreparedAction(CocRequest $cocRequest)
    {
        if (null !== $cocRequest->getPreparedAt()) {
            $this->get('session')->getFlashBag()->add('error', 'Dit vog is al klaar gezet!');

            return $this->redirectToRoute('admin-vog');
        }

        $command = new PrepareCocRequestCommand($cocRequest->getId());
        $this->get('command_bus')->handle($command);

        $this->get('session')->getFlashBag()->add('success', 'Dit vog is klaar gezet!');

        return $this->redirectToRoute('admin-vog');
    }

    /**
     * @Route("/vog/{id}/received", name="admin-vog-received")
     *
     * @param CocRequest $cocRequest
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function vogReceivedAction(CocRequest $cocRequest)
    {
        if (null !== $cocRequest->getReceivedAt()) {
            $this->get('session')->getFlashBag()->add('error', 'Dit vog is al ontvangen!');

            return $this->redirectToRoute('admin-vog');
        }

        $command = new ReceiveCocRequestCommand($cocRequest->getId());
        $this->get('command_bus')->handle($command);

        $this->get('session')->getFlashBag()->add('success', 'Dit vog is gemarkeerd als ontvangen!');

        return $this->redirectToRoute('admin-vog');
    }
}
