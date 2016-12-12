<?php

namespace CoderDojo\WebsiteBundle\Service;

use CL\Slack\Model\Attachment;
use CoderDojo\WebsiteBundle\Entity\Dojo as InternalDojo;
use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Entity\DojoEvent;
use CoderDojo\WebsiteBundle\Service\ZenModel\Dojo as ExternalDojo;
use Doctrine\Bundle\DoctrineBundle\Registry;
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
     * SyncService constructor.
     * @param ZenApiService $zen
     * @param Registry $doctrine
     * @param SlackService $slackService
     */
    public function __construct(ZenApiService $zen, Registry $doctrine, SlackService $slackService)
    {
        $this->zen = $zen;
        $this->doctrine = $doctrine->getManager();
        $this->slackService = $slackService;
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

                $newEvent = new DojoEvent();
                $newEvent->setZenId($externalEvent->getZenId());
                $newEvent->setName($externalEvent->getName());
                $newEvent->setDojo($internalDojo);
                $newEvent->setType(DojoEvent::TYPE_ZEN);
                $newEvent->setDate($externalEvent->getStartTime());
                $newEvent->setUrl($internalDojo->getZenUrl());
                $internalDojo->addEvent($newEvent);

                $this->doctrine->persist($newEvent);

                $progressbar->advance();
                $countNew++;
            } else {
                $progressbar->setMessage('Internal event found');

                $internalEvent->setName($externalEvent->getName());
                $internalEvent->setDate($externalEvent->getStartTime());
                $internalEvent->setUrl($internalDojo->getZenUrl());

                $progressbar->advance();
                $countUpdated++;
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

        $attachment = new Attachment();
        $attachment->setFallback($countNew . " events added.");
        $attachment->setText($countNew . " events added.");
        $attachment->setColor('good');
        $attachments[] = $attachment;

        $attachment = new Attachment();
        $attachment->setFallback($countUpdated . " events updated.");
        $attachment->setText($countUpdated . " events updated.");
        $attachment->setColor('warning');
        $attachments[] = $attachment;

        $attachment = new Attachment();
        $attachment->setFallback($countNoMatch . " events not matched.");
        $attachment->setText($countNoMatch . " events not matched.");
        $attachment->setColor('danger');
        $attachments[] = $attachment;

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