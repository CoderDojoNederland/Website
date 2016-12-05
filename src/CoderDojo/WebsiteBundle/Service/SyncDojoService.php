<?php

namespace CoderDojo\WebsiteBundle\Service;

use CoderDojo\WebsiteBundle\Entity\Dojo as InternalDojo;
use CoderDojo\WebsiteBundle\Service\ZenModel\Dojo as ExternalDojo;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\NonUniqueResultException;
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
     * SyncService constructor.
     * @param ZenApiService $zen
     * @param Registry $doctrine
     */
    public function __construct(ZenApiService $zen, Registry $doctrine)
    {
        $this->zen = $zen;
        $this->doctrine = $doctrine->getManager();
    }

    public function run(OutputInterface $output)
    {
        $output->writeln('**********************************');
        $output->writeln('Starting sync for dojos');

        $progressbar = $this->newProgressBar($output);

        $externalDojos = $this->zen->getDojos();

        $progressbar->start(count($externalDojos));
        $progressbar->setMessage('Iterating dojos...');

        foreach($externalDojos as $externalDojo) {
            $progressbar->setMessage('Handling ' . $externalDojo->getName());

            if (true === $externalDojo->isRemoved()) {
                $progressbar->setMessage('Removing ' . $externalDojo->getName());

                $this->removeInternalDojo($externalDojo);

                $progressbar->advance();
                continue;
            }

            $internalDojo = $this->getInternalDojo(
                $externalDojo->getZenId(),
                $externalDojo->getCity(),
                $externalDojo->getTwitter(),
                $externalDojo->getEmail()
            );

            if (null !== $internalDojo) {
                $progressbar->setMessage('Matched internal dojo: ' . $internalDojo->getName());

                $this->updateInternalDojo($internalDojo, $externalDojo);

                $progressbar->advance();
                $this->countUpdated++;

                continue;
            }

            $progressbar->setMessage('Creating new dojo');

            $this->createInternalDojo($externalDojo);

            $progressbar->advance();
            $this->countNew++;
        }

        $progressbar->setMessage('Flushing');
        $this->doctrine->flush();

        $progressbar->setMessage('Finished syncing dojos!');
        $progressbar->finish();

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
    private function getInternalDojo($zenId, $city, $twitter, $email)
    {
        $internalDojo = $this->doctrine
            ->getRepository('CoderDojoWebsiteBundle:Dojo')
            ->findOneBy(['zenId' => $zenId]);

        if (null !== $internalDojo) {
            return $internalDojo;
        }

        try {
            $internalDojo = $this->doctrine
                ->getRepository('CoderDojoWebsiteBundle:Dojo')
                ->getForExternal($city, $email, $twitter);
        } catch (NonUniqueResultException $exception) {

        }

        return $internalDojo;
    }

    /**
     * Updates the internal dojo with data from the external dojo
     *
     * @param InternalDojo $internalDojo
     * @param ExternalDojo $externalDojo
     */
    private function updateInternalDojo($internalDojo, $externalDojo)
    {
        $internalDojo->setZenId($externalDojo->getZenId());
        $internalDojo->setZenCreatorEmail($externalDojo->getZenCreatorEmail());
        $internalDojo->setZenUrl($externalDojo->getZenUrl());
        $internalDojo->setName($externalDojo->getName());
        $internalDojo->setLat($externalDojo->getLat());
        $internalDojo->setLon($externalDojo->getLon());
        $internalDojo->setEmail($externalDojo->getEmail());
        $internalDojo->setWebsite($externalDojo->getWebsite());
        $internalDojo->setTwitter($externalDojo->getTwitter());
    }

    /**
     * Creates a new Internal Dojo with data from the external dojo
     *
     * @param ExternalDojo $externalDojo
     */
    private function createInternalDojo($externalDojo)
    {
        $internalDojo = new InternalDojo(
            $externalDojo->getZenId(),
            $externalDojo->getName(),
            $externalDojo->getCity(),
            $externalDojo->getLat(),
            $externalDojo->getLon(),
            $externalDojo->getEmail(),
            $externalDojo->getWebsite(),
            $externalDojo->getTwitter()
        );

        $internalDojo->setZenCreatorEmail($externalDojo->getZenCreatorEmail());
        $internalDojo->setZenUrl($externalDojo->getZenUrl());

        $this->doctrine->persist($internalDojo);
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
     * @param ExternalDojo $externalDojo
     */
    private function removeInternalDojo(ExternalDojo $externalDojo)
    {
        $internalDojo = $this->getInternalDojo(
            $externalDojo->getZenId(),
            $externalDojo->getCity(),
            $externalDojo->getTwitter(),
            $externalDojo->getEmail()
        );

        if (null === $internalDojo) {
            return;
        }

        foreach ($internalDojo->getEvents() as $event) {
            $this->doctrine->remove($event);
        }

        foreach ($internalDojo->getOwners() as $owner) {
            $internalDojo->removeOwner($owner);
            $this->doctrine->remove($owner);
        }

        foreach ($internalDojo->getMentorRequests() as $mentorRequest) {
            $this->doctrine->remove($mentorRequest);
        }

        $claims = $this->doctrine->getRepository('CoderDojoWebsiteBundle:Claim')->findBy(['dojo'=>$internalDojo]);

        foreach ($claims as $claim) {
            $this->doctrine->remove($claim);
        }

        $this->doctrine->flush();

        $this->doctrine->remove($internalDojo);

        $this->doctrine->flush();

        $this->countRemoved++;
    }
}