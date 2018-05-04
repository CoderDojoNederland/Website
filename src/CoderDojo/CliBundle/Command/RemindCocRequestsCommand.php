<?php

namespace CoderDojo\CliBundle\Command;

use CoderDojo\WebsiteBundle\Command\ExpireCocRequestCommand;
use CoderDojo\WebsiteBundle\Entity\CocRequest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemindCocRequestsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('coc:remind')
            ->setDescription('Reminds requests which are 1 week from expiring')
            ->addOption('dry-run', 'd')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getContainer()->get('doctrine')->getRepository('CoderDojoWebsiteBundle:CocRequest');
        $requests = $repository->getInNeedOfReminder();

        $bar = new ProgressBar($output);
        $bar->setFormat('%current% of %max%'.PHP_EOL.'[%bar%] %percent:3s%%'.PHP_EOL.'%message% '.PHP_EOL);
        $bar->setBarCharacter('<info>></info>');
        $bar->setProgressCharacter('<comment>|</comment>');
        $bar->setEmptyBarCharacter('<info>.</info>');
        $bar->setMessage('Getting started');
        $bar->start(count($requests));

        /** @var CocRequest $request */
        foreach($requests as $request) {
            $msgChampion = \Swift_Message::newInstance()
                ->setSubject('🔔 Herinnering VOG Aanvraag')
                ->setFrom('contact@coderdojo.nl', 'CoderDojo Nederland')
                ->setReplyTo('contact@coderdojo.nl')
                ->setTo($request->getRequestedBy()->getEmail())
                ->setBcc('chris+accept-privacy@coderdojo.nl')
                ->setContentType('text/html')
                ->setBody(
                     $this->getContainer()->get('templating')->render(
                         ':Dashboard:Email/Coc/reminder_champion.html.twig',
                         ['coc' => $request]
                     )
                )
            ;

            $msgVolunteer = \Swift_Message::newInstance()
                ->setSubject('🔔 Herinnering VOG Aanvraag')
                ->setFrom('contact@coderdojo.nl', 'CoderDojo Nederland')
                ->setReplyTo('contact@coderdojo.nl')
                ->setTo($request->getEmail())
                ->setBcc('chris+accept-privacy@coderdojo.nl')
                ->setContentType('text/html')
                ->setBody(
                    $this->getContainer()->get('templating')->render(
                        ':Dashboard:Email/Coc/reminder_volunteer.html.twig',
                        ['coc' => $request]
                    )
                )
            ;

            if (!$input->getOption('dry-run')) {
                $this->getContainer()->get('mailer')->send($msgChampion);
                $this->getContainer()->get('mailer')->send($msgVolunteer);
                $request->expiryReminderSent();
                $this->getContainer()->get('doctrine')->getManager()->persist($request);
                $this->getContainer()->get('doctrine')->getManager()->flush();
            }

            $bar->advance();
        }

        $bar->finish();

        if ($bar->getProgress() > 0) {
            $this->getContainer()->get('coder_dojo.website_bundle.slack_service')->sendToChannel(
                '#website-nl',
                sprintf('Er zijn %d VOG\'s herinnerd', $bar->getProgress())
            );
        }
    }
}
