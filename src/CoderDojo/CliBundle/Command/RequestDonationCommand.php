<?php

namespace CoderDojo\CliBundle\Command;

use CoderDojo\WebsiteBundle\Command\ExpireCocRequestCommand;
use CoderDojo\WebsiteBundle\Entity\Club100;
use CoderDojo\WebsiteBundle\Entity\CocRequest;
use CoderDojo\WebsiteBundle\Entity\Donation;
use CoderDojo\WebsiteBundle\Service\NextDonationFinder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RequestDonationCommand extends ContainerAwareCommand
{
    /**
     * CronJobs
     *
     * 30 18 1 4,10 * => Semi-yearly
     * 30 18 1 *\3 *  => Quarterly
     * 30 18 1 6 *    => Yearly
     */
    protected function configure()
    {
        $this
            ->setName('donations:request')
            ->setDescription('Requests donations from our users')
            ->addArgument('interval', InputArgument::REQUIRED)
            ->addOption('dry-run', 'd')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine   = $this->getContainer()->get('doctrine');
        $repository = $doctrine->getRepository(Club100::class);
        $interval   = $input->getArgument('interval');
        $members    = $repository->getAllActive(false, $interval);

        $io = new SymfonyStyle($input, $output);
        $io->section('Starting with '.count($members).' member');

        /** @var Club100 $member */
        foreach($members as $member) {
            if ($member->getConfirmationUrl()) {
                $io->writeln(sprintf('Member %s %s has already started eCurring!', $member->getFirstName(), $member->getLastName()));
                $io->newLine(2);
                continue;
            }

            $ecurring = $this->getContainer()->get('coder_dojo.website_bundle.ecurring');
            $memberId = $ecurring->createCustomer($member);
            $confirmationUrl = $ecurring->createSubscription($memberId, $member->getInterval(), $member->getHash());
            $member->setConfirmationUrl($confirmationUrl);
            $doctrine->getManager()->flush();

            /**
             * Send email to dojo contact address
             */
            $message = \Swift_Message::newInstance()
                ->setSubject('Jouw Club van 100 donatie')
                ->setFrom('contact@coderdojo.nl', 'CoderDojo Nederland')
                ->setTo($member->getEmail())
                ->setBcc('website+club100@coderdojo.nl')
                ->setContentType('text/html')
                ->setBody(
                    $this->getContainer()->get('templating')->render(':Pages:ClubVan100/Email/payment_request.html.twig', [
                        'member' => $member, 'confirmationUrl'=>$confirmationUrl
                    ])
                );

            $this->getContainer()->get('mailer')->send($message);

            $io->writeln(sprintf('Member %s %s has received a request for donation %s', $member->getFirstName(), $member->getLastName(), $donation->getUuid()));
            $io->newLine(2);
        }

        $io->success('Done all members for this period!');
    }
}
