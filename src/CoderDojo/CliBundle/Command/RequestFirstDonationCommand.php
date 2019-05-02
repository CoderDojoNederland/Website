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

class RequestFirstDonationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('donations:first:request')
            ->setDescription('Requests donations from our users')
            ->addOption('dry-run', 'd')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getContainer()->get('doctrine')->getRepository(Club100::class);
        $donationRepository = $this->getContainer()->get('doctrine')->getRepository(Donation::class);
        $members = $repository->findBy(['confirmed' => true]);

        $bar = new ProgressBar($output);
        $bar->setFormat('%current% of %max%'.PHP_EOL.'[%bar%] %percent:3s%%'.PHP_EOL.'%message% '.PHP_EOL);
        $bar->setBarCharacter('<info>></info>');
        $bar->setProgressCharacter('<comment>|</comment>');
        $bar->setEmptyBarCharacter('<info>.</info>');
        $bar->setMessage('Getting started');
        $bar->start(count($members));

        /** @var Club100 $member */
        foreach($members as $member) {
            if (NextDonationFinder::shouldSendFirstRequest($member) === false) {
                continue;
            }

            $donation = new Donation($member);

            $existing = $donationRepository->findOneBy(
                [
                    'member' => $member,
                    'year' => $donation->getYear(),
                    'quarter' => $donation->getQuarter()
                ]
            );

            if ($existing) {
                continue;
            }

            $this->getContainer()->get('doctrine')->getManager()->persist($donation);
            $this->getContainer()->get('doctrine')->getManager()->flush();

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
                 $this->getContainer()->get('templating')->render(':Pages:ClubVan100/Email/first_donation.html.twig',
                   [
                       'member' => $member,
                       'nextDonation' => NextDonationFinder::findNextDonation($member),
                       'donation' => $donation
                   ]
                 )
             );

            $this->getContainer()->get('mailer')->send($message);

            $bar->advance();
        }

        $bar->finish();
    }
}
