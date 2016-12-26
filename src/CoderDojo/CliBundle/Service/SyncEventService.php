<?php

namespace CoderDojo\CliBundle\Service;

use CL\Slack\Model\Attachment;
use CoderDojo\CliBundle\Service\ZenModel\Event;
use CoderDojo\WebsiteBundle\Command\CreateEventCommand;
use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Entity\DojoEvent;
use CoderDojo\WebsiteBundle\Service\SlackService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class SyncEventService
{
    /**
     * @var ZenApiService
     */
    private $zen;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var SlackService
     */
    private $slackService;

    /**
     * @var MessageBus
     */
    private $commandBus;

    /**
     * SyncService constructor.
     * @param ZenApiService $zen
     * @param Registry $doctrine
     * @param SlackService $slackService
     * @param MessageBus $commandBus
     */
    public function __construct(
        ZenApiService $zen,
        Registry $doctrine,
        SlackService $slackService,
        MessageBus $commandBus
    ) {
        $this->zen = $zen;
        $this->doctrine = $doctrine->getManager();
        $this->slackService = $slackService;
        $this->commandBus = $commandBus;
    }

    public function run(OutputInterface $output)
    {
        $output->writeln('**********************************');
        $output->writeln('Starting sync for Events');

        $progressbar = $this->newProgressBar($output);

        $zenIds = $this->doctrine->getRepository('CoderDojoWebsiteBundle:Dojo')->getZenIds();

        $externalEvents = $this->zen->getEvents($zenIds);

        $progressbar->start(count($externalEvents));
        $progressbar->setMessage('Iterating Events...');

        $countNew = 0;
        $countUpdated = 0;
        $countNoMatch = 0;

        foreach($externalEvents as $externalEvent) {
            $progressbar->setMessage('Handling ' . $externalEvent->getName());

            $internalEvent = $this->doctrine
                ->getRepository('CoderDojoWebsiteBundle:DojoEvent')
                ->findOneBy(['zenId'=>$externalEvent->getZenId()]);

            /** @var Dojo $dojo */
            $internalDojo = $this->doctrine
                ->getRepository('CoderDojoWebsiteBundle:Dojo')
                ->findOneBy(['zenId'=>$externalEvent->getZenDojoId()]);

            if (null === $internalDojo) {
                $progressbar->setMessage('No internal dojo found!');
                $progressbar->advance();
                $countNoMatch++;

                continue;
            }

            if (null === $internalEvent){
                $progressbar->setMessage('No internal event found');

                $command = new CreateEventCommand(
                    $internalDojo->getId(),
                    $externalEvent->getName(),
                    $externalEvent->getStartTime(),
                    $internalDojo->getZenUrl(),
                    $externalEvent->getZenId(),
                    DojoEvent::TYPE_ZEN
                );

                $this->commandBus->handle($command);

                $progressbar->advance();
                $countNew++;
            } else {
                $progressbar->setMessage('Internal event found');

                $internalModel = Event::CreateFromEntity($internalEvent);

                if ($internalModel != $externalEvent) {
                    $internalEvent->setName($externalEvent->getName());
                    $internalEvent->setDate($externalEvent->getStartTime());
                    $internalEvent->setUrl($internalDojo->getZenUrl());

                    $countUpdated++;
                }

                $progressbar->advance();
            }
        }

        $progressbar->setMessage('Flushing');
        $this->doctrine->flush();
        $progressbar->setMessage('Finished syncing Events!');
        $progressbar->finish();
        $output->writeln($countNew . ' New events added');
        $output->writeln($countUpdated . ' Existing events updated');
        $output->writeln($countNoMatch . ' events could not be matched with a dojo');

        $message = "Zen synchronizer just handled events.";
        $attachments = [];

        if (0 < $countNew){
            $attachment = new Attachment();
            $attachment->setFallback($countNew . " events added.");
            $attachment->setText($countNew . " events added.");
            $attachment->setColor('good');
            $attachments[] = $attachment;
        }

        if (0 < $countUpdated) {
            $attachment = new Attachment();
            $attachment->setFallback($countUpdated . " events updated.");
            $attachment->setText($countUpdated . " events updated.");
            $attachment->setColor('warning');
            $attachments[] = $attachment;
        }

        if (0 < $countNoMatch) {
            $attachment = new Attachment();
            $attachment->setFallback($countNoMatch . " events not matched.");
            $attachment->setText($countNoMatch . " events not matched.");
            $attachment->setColor('danger');
            $attachments[] = $attachment;
        }

        if (0 === $countNew && 0 === $countUpdated && 0 === $countNoMatch) {
            return;
        }
        $this->slackService->sendToChannel('#website-nl', $message, $attachments);
    }

    /**
     * @param OutputInterface $output
     * @return ProgressBar
     */
    private function newProgressBar(OutputInterface $output)
    {
        $progressbar = new ProgressBar($output);
        $format = implode(
            "\n",
            [
                'Processing ',
                '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%',
                'Status: %message:10s%',
                ''
            ]
        );
        $progressbar->setFormat($format);
        return $progressbar;
    }
}