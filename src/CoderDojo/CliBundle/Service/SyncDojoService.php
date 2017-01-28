<?php

namespace CoderDojo\CliBundle\Service;

use CL\Slack\Model\Attachment;
use CL\Slack\Model\AttachmentField;
use CoderDojo\CliBundle\Service\ZenModel\Dojo;
use CoderDojo\WebsiteBundle\Command\CreateDojoCommand;
use CoderDojo\WebsiteBundle\Command\RemoveDojoCommand;
use CoderDojo\WebsiteBundle\Entity\Dojo as InternalDojo;
use CoderDojo\CliBundle\Service\ZenModel\Dojo as ExternalDojo;
use CoderDojo\WebsiteBundle\Service\SlackService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\NonUniqueResultException;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class SyncDojoService
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
     * @var int
     */
    private $countNew = 0;

    /**
     * @var int
     */
    private $countUpdated = 0;

    /**
     * @var int
     */
    private $countRemoved = 0;

    /**
     * @var CreateDojoCommand[]
     */
    private $unmatched = [];

    /**
     * @var SlackService
     */
    private $slackService;

    /**
     * @var MessageBus
     */
    private $commandBus;

    /**
     * @var ProgressBar
     **/
    private $progressBar = null;

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
        $output->writeln('Starting sync for dojos');

        $this->progressBar = $this->newProgressBar($output);

        $externalDojos = $this->zen->getDojos();

        $this->progressBar->start(count($externalDojos));
        $this->progressBar->setMessage('Iterating dojos...');


        foreach($externalDojos as $externalDojo) {
            $this->progressBar->setMessage('Handling ' . $externalDojo->getName());

            /**
             * remove if dojo is marked as removed
             */
            if (true === $externalDojo->isRemoved()) {
                $this->removeInternalDojo($externalDojo);

                continue;
            }

            /**
             * Try to match a single internal dojo
             */
            try {
                $internalDojo = $this->getInternalDojo(
                    $externalDojo->getZenId(),
                    $externalDojo->getCity(),
                    $externalDojo->getTwitter(),
                    $externalDojo->getEmail()
                );
            } catch (NonUniqueResultException $exception) {
                $this->unmatched[] = $externalDojo;

                continue;
            }

            /**
             * If there is no internal dojo found, we need to create this one
             */
            if (null === $internalDojo) {
                $this->createDojo($externalDojo);

                continue;
            }

            /**
             * No removal, no creation, one thing left, update!
             */
            $internalModel = CreateDojoCommand::CreateFromEntity($internalDojo);

            if ($externalDojo != $internalModel) {
                $this->updateInternalDojo($internalDojo, $externalDojo);

                continue;
            }

            $this->progressBar->setMessage('No action needed for ' . $externalDojo->getName());
        }

        $this->progressBar->setMessage('Flushing');
        $this->doctrine->flush();

        $this->progressBar->setMessage('Finished syncing dojos!');
        $this->progressBar->finish();

        $output->writeln($this->countNew . ' New dojos added');
        $output->writeln($this->countUpdated . ' Existing dojos updated');
        $output->writeln($this->countRemoved . ' Existing dojos removed');

        $this->notifySlack();
    }

    /**
     * Tries to find a corresponding internal dojo
     *
     * @param $zenId
     * @param $city
     * @param $twitter
     * @param $email
     * @return InternalDojo|null
     */
    private function getInternalDojo($zenId, $city, $twitter, $email)
    {
        $internalDojo = $this->doctrine
            ->getRepository('CoderDojoWebsiteBundle:Dojo')
            ->findOneBy(['zenId' => $zenId]);

        if (null !== $internalDojo) {
            return $internalDojo;
        }

        $internalDojo = $this->doctrine
            ->getRepository('CoderDojoWebsiteBundle:Dojo')
            ->getForExternalWithoutZenId($city, $email, $twitter);

        return $internalDojo;
    }

    /**
     * Updates the internal dojo with data from the external dojo
     *
     * @param InternalDojo $internalDojo
     * @param CreateDojoCommand $externalDojo
     */
    private function updateInternalDojo(InternalDojo $internalDojo, CreateDojoCommand $externalDojo)
    {
        $this->progressBar->setMessage('Matched internal dojo: ' . $internalDojo->getName());

        $internalDojo->setZenId($externalDojo->getZenId());
        $internalDojo->setZenCreatorEmail($externalDojo->getZenCreatorEmail());
        $internalDojo->setZenUrl($externalDojo->getZenUrl());
        $internalDojo->setName($externalDojo->getName());
        $internalDojo->setLat($externalDojo->getLat());
        $internalDojo->setLon($externalDojo->getLon());
        $internalDojo->setEmail($externalDojo->getEmail());
        $internalDojo->setWebsite($externalDojo->getWebsite());
        $internalDojo->setTwitter($externalDojo->getTwitter());
        $internalDojo->setCity($externalDojo->getCity());

        $this->countUpdated++;
        $this->progressBar->advance();
    }

    /**
     * Create new progressbar to track progress
     *
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

    /**
     * Remove an internal dojo when an external dojo is no longer active
     *
     * @param CreateDojoCommand $externalDojo
     */
    private function removeInternalDojo(CreateDojoCommand $externalDojo)
    {
        $this->progressBar->setMessage('Removing ' . $externalDojo->getName());

        try {
            $internalDojo = $this->getInternalDojo(
                $externalDojo->getZenId(),
                $externalDojo->getCity(),
                $externalDojo->getTwitter(),
                $externalDojo->getEmail()
            );
        } catch (NonUniqueResultException $exception) {

            return;
        }

        if (null === $internalDojo) {
            return;
        }

        $command = new RemoveDojoCommand($internalDojo->getId());
        $this->commandBus->handle($command);

        $this->countRemoved++;

        $this->progressBar->advance();
    }

    /**
     * Shoot the create dojo command into the command bus
     *
     * @param $externalDojo
     */
    private function createDojo($externalDojo)
    {
        $this->progressBar->setMessage('Creating new dojo');

        $this->commandBus->handle($externalDojo);

        $this->progressBar->advance();
        $this->countNew++;
    }

    /**
     * Created notification for slack to keep us up to date on the sync process
     */
    private function notifySlack()
    {
        if (
            0 === $this->countNew &&
            0 === $this->countUpdated &&
            0 === $this->countRemoved &&
            0 === count($this->unmatched)
        ) {
            return;
        }

        $message = "Zen synchronizer just handled dojo's.";
        $attachments = [];

        if (0 < $this->countNew) {
            $attachment = new Attachment();
            $attachment->setFallback($this->countNew . " dojo's added.");
            $attachment->setText($this->countNew . " dojo's added.");
            $attachment->setColor('good');
            $attachments[] = $attachment;
        }

        if (0 < $this->countUpdated) {
            $attachment = new Attachment();
            $attachment->setFallback($this->countUpdated . " dojo's updated.");
            $attachment->setText($this->countUpdated . " dojo's updated.");
            $attachment->setColor('warning');
            $attachments[] = $attachment;
        }

        if (0 < $this->countRemoved) {
            $attachment = new Attachment();
            $attachment->setFallback($this->countRemoved . " dojo's removed.");
            $attachment->setText($this->countRemoved . " dojo's removed.");
            $attachment->setColor('danger');
            $attachments[] = $attachment;
        }

        $this->slackService->sendToChannel('#general', $message, $attachments);

        foreach($this->unmatched as $unmatched) {
            $attachment = new Attachment();
            $attachment->setFallback(sprintf('This dojo resulted in multiple internal possibilities: %s (zen: $s)', $unmatched->getName(), $unmatched->getZenId()));
            $attachment->setText('A dojo resulted in multiple internal possibilities');
            $attachment->setColor('danger');

            $nameField = new AttachmentField();
            $nameField->setTitle('Name');
            $nameField->setValue($unmatched->getName());
            $nameField->setShort(true);

            $cityField = new AttachmentField();
            $cityField->setTitle('City');
            $cityField->setValue($unmatched->getCity());
            $cityField->setShort(true);

            $zenIdField = new AttachmentField();
            $zenIdField->setTitle('Zen ID');
            $zenIdField->setValue($unmatched->getZenId());
            $zenIdField->setShort(true);

            $zenUrlField = new AttachmentField();
            $zenUrlField->setTitle('Zen Url');
            $zenUrlField->setValue($unmatched->getZenUrl());
            $zenUrlField->setShort(true);

            $attachment->addField($nameField);
            $attachment->addField($cityField);
            $attachment->addField($zenIdField);
            $attachment->addField($zenUrlField);

            $this->slackService->sendToChannel('#website-nl', 'We couldn\'t match this dojo internally.', [$attachment]);
        }
    }
}