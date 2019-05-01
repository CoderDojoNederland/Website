<?php

namespace CoderDojo\CliBundle\Command;

use CoderDojo\WebsiteBundle\Command\ExpireCocRequestCommand;
use CoderDojo\WebsiteBundle\Entity\Club100;
use CoderDojo\WebsiteBundle\Entity\CocRequest;
use CoderDojo\WebsiteBundle\Entity\Donation;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RequestDonationCommand extends ContainerAwareCommand
{
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
        $repository = $this->getContainer()->get('doctrine')->getRepository(Club100::class);
        $donationRepository = $this->getContainer()->get('doctrine')->getRepository(Donation::class);

        $interval = $input->getArgument('interval');
        $quarter = $interval === 'yearly' ? null : ceil((new \DateTime())->format('m') / 3);

        $members = $repository->findBy(['interval' => $interval]);

        $bar = new ProgressBar($output);
        $bar->setFormat('%current% of %max%'.PHP_EOL.'[%bar%] %percent:3s%%'.PHP_EOL.'%message% '.PHP_EOL);
        $bar->setBarCharacter('<info>></info>');
        $bar->setProgressCharacter('<comment>|</comment>');
        $bar->setEmptyBarCharacter('<info>.</info>');
        $bar->setMessage('Getting started');
        $bar->start(count($members));

        $year     = (new \DateTime())->format('Y');

        /** @var Club100 $member */
        foreach($members as $member) {
            $existing = $donationRepository->findOneBy(['member' => $member, 'year' => $year, 'quarter' => $quarter]);

            if ($existing) {
                continue;
            }

            $donation = new Donation($member, $year, $quarter);
            $this->getContainer()->get('doctrine')->getManager()->persist($donation);
            $this->getContainer()->get('doctrine')->getManager()->flush();

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
                    $this->getContainer()->get('templating')->render(':Pages:ClubVan100/Email/payment_request.html.twig', ['member' => $member, 'donation'=>$donation])
                );

            $this->getContainer()->get('mailer')->send($message);

            $bar->advance();
        }

        $bar->finish();
    }
}
