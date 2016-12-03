<?php

namespace CoderDojo\WebsiteBundle\Service;

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
    }

    /**
     * @param $zenId
     * @param $city
     *
     * @return InternalDojo|null
     */
    private function getInternalDojo($zenId, $city)
    {
        $internalDojo = $this->doctrine
            ->getRepository('CoderDojoWebsiteBundle:Dojo')
            ->findOneBy(['zenId' => $zenId]);

        if (null !== $internalDojo) {
            return $internalDojo;
        }

        $internalDojos = $this->doctrine
            ->getManager()
            ->getRepository('CoderDojoWebsiteBundle:Dojo')
            ->findBy(['city' => $city, 'zenId' => null]);

        if (1 === count($internalDojos)) {
            return $internalDojos[0];
        }

        return null;
    }

    /**
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