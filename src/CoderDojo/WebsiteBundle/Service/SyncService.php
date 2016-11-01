<?php

namespace CoderDojo\WebsiteBundle\Service;

use CoderDojo\WebsiteBundle\Entity\Dojo as InternalDojo;
use CoderDojo\WebsiteBundle\Service\ZenModel\Dojo as ExternalDojo;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Console\Output\OutputInterface;

class SyncService
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
        $output->writeln('Starting sync for dojos');

        $externalDojos = $this->zen->getDojos();

        $output->writeln('Found ' . count($externalDojos) . ' External Dojos');
        $output->writeln('Iterating dojos...');

        foreach($externalDojos as $externalDojo) {
            $output->writeln('Starting on ' . $externalDojo->getName());

            $internalDojo = $this->getInternalDojo($externalDojo->getZenId(), $externalDojo->getCity());

            if (null !== $internalDojo) {
                $output->writeln('Matched internal dojo: ' . $internalDojo->getName());
                $output->writeln('Updating.');

                $this->updateInternalDojo($internalDojo, $externalDojo);

                $output->writeln('#######');

                continue;
            }

            $output->writeln('No internal matched.');
            $output->writeln('Creating new one');

            $this->createInternalDojo($externalDojo);

            $output->writeln('#######');
        }

        $output->writeln('Flushing');
        $this->doctrine->flush();
        $output->writeln('Finished!');
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
}