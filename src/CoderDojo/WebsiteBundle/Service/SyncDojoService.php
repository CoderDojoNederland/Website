<?php

namespace CoderDojo\WebsiteBundle\Service;

use CoderDojo\WebsiteBundle\Entity\Dojo as InternalDojo;
use CoderDojo\WebsiteBundle\Service\ZenModel\Dojo as ExternalDojo;
use Doctrine\Bundle\DoctrineBundle\Registry;
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

        $countNew = 0;
        $countUpdated = 0;

        foreach($externalDojos as $externalDojo) {
            $progressbar->setMessage('Handling ' . $externalDojo->getName());

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
                $countUpdated++;

                continue;
            }

            $progressbar->setMessage('Creating new one');

            $this->createInternalDojo($externalDojo);

            $progressbar->advance();
            $countNew++;
        }

        $progressbar->setMessage('Flushing');
        $this->doctrine->flush();
        $progressbar->setMessage('Finished syncing dojos!');
        $progressbar->finish();
        $output->writeln($countNew . ' New dojos added');
        $output->writeln($countUpdated . ' Existing dojos updated');
    }

    /**
     * @param $zenId
     * @param $city
     *
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

        $internalDojos = $this->doctrine
            ->getRepository('CoderDojoWebsiteBundle:Dojo')
            ->findBy(['city' => $city, 'twitter'=>$twitter, 'zenId' => null]);

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