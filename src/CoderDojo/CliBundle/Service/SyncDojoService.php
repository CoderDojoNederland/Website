<?php

namespace CoderDojo\CliBundle\Service;

use CoderDojo\WebsiteBundle\Command\CreateDojoCommand;
use CoderDojo\WebsiteBundle\Command\RemoveDojoCommand;
use CoderDojo\WebsiteBundle\Entity\Dojo as InternalDojo;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\NonUniqueResultException;
use GuzzleHttp\Client;
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
     * @param MessageBus $commandBus
     */
    public function __construct(
        ZenApiService $zen,
        Registry $doctrine,
        MessageBus $commandBus
    ) {
        $this->zen = $zen;
        $this->doctrine = $doctrine->getManager();
        $this->commandBus = $commandBus;
    }

    public function run(OutputInterface $output)
    {
        $output->writeln('**********************************');
        $output->writeln('Starting sync for dojos');

        $this->progressBar = $this->newProgressBar($output);

        $externalDojos = $this->zen->getNlDojos();
        $externalDojos = array_merge($externalDojos, $this->zen->getBeDojos());

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

            if ($externalDojo != $internalModel || $internalDojo->getProvince() === null || $internalDojo->getCountry() === null) {
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
    private function getInternalDojo($zenId, $city, $twitter, $email): ?InternalDojo
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

        $verifiedAt = $externalDojo->getVerifiedAt() ? new \DateTime(
            $externalDojo->getVerifiedAt()
        ) : $internalDojo->getVerifiedAt();

        $internalDojo->setVerifiedAt($verifiedAt);
        $internalDojo->setZenCreatorEmail($externalDojo->getZenCreatorEmail() ?? $internalDojo->getZenCreatorEmail());
        $internalDojo->setZenUrl($externalDojo->getZenUrl());
        $internalDojo->setName($externalDojo->getName());
        $internalDojo->setLat($externalDojo->getLat());
        $internalDojo->setLon($externalDojo->getLon());
        $internalDojo->setEmail($externalDojo->getEmail());
        $internalDojo->setWebsite($externalDojo->getWebsite());
        $internalDojo->setTwitter($externalDojo->getTwitter() ?? $internalDojo->getTwitter());
        $internalDojo->setCity($externalDojo->getCity() ?? $internalDojo->getCity());
        $internalDojo->setCountry($externalDojo->getCountry());

        /**
         * Also update the url for child events if it changed.
         * Make sure we only do it for Zen events to not overwrite custom events
         */
        foreach($internalDojo->getEvents() as $event) {
            if (null != $event->getZenId()) {
                $event->setUrl($externalDojo->getZenUrl());
            }
        }

        if ($internalDojo->getProvince() === null) {
            $client = new Client();
            $response = $client->get(
                'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$internalDojo->getLat().','.$internalDojo->getLon().'&sensor=false&key=AIzaSyBy7V91eQVB-Uo70MTNxv-oErLPkgKtWJM'
            );
            $result = json_decode($response->getBody()->getContents(), true);
            if (count($result['results']) > 0) {
                $components = $result['results'][0]['address_components'];
                foreach($components as $component) {
                    $levels = array_values($component['types']);
                    foreach($levels as $key => $value) {
                        if ($value === 'administrative_area_level_1') {
                            $internalDojo->setProvince($component['long_name']);
                        }
                    }
                }
            }
        }

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
}
