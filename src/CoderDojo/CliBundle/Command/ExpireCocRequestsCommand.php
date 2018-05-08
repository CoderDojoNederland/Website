<?php

namespace CoderDojo\CliBundle\Command;

use CoderDojo\WebsiteBundle\Command\ExpireCocRequestCommand;
use CoderDojo\WebsiteBundle\Entity\CocRequest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExpireCocRequestsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('coc:expire')
            ->setDescription('Expires requests which are older then 30 days.')
            ->addOption('dry-run', 'd')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getContainer()->get('doctrine')->getRepository('CoderDojoWebsiteBundle:CocRequest');
        $requests = $repository->getReadyToExpire();

        $bar = new ProgressBar($output);
        $bar->setFormat('%current% of %max%'.PHP_EOL.'[%bar%] %percent:3s%%'.PHP_EOL.'%message% '.PHP_EOL);
        $bar->setBarCharacter('<info>></info>');
        $bar->setProgressCharacter('<comment>|</comment>');
        $bar->setEmptyBarCharacter('<info>.</info>');
        $bar->setMessage('Getting started');
        $bar->start(count($requests));

        /** @var CocRequest $request */
        foreach($requests as $request) {
            $command = new ExpireCocRequestCommand($request->getId());

            if (!$input->getOption('dry-run')) {
                $this->getContainer()->get('command_bus')->handle($command);
            }

            $bar->advance();
        }

        $bar->finish();

        if ($bar->getProgress() > 0) {
            $this->getContainer()->get('coder_dojo.website_bundle.slack_service')->sendToChannel(
                '#website-nl',
                sprintf('🚫 Er zijn %d VOG\'s verlopen', $bar->getProgress())
            );
        }
    }
}
