<?php

namespace Coderdojo\WebsiteBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Coderdojo\WebsiteBundle\Entity\DojoEvent;

/**
 * @codeCoverageIgnore
 */
class LoadDojoEventData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $dojo = new DojoEvent();

        $dojo->setName('CoderDojo #12')
            ->setDate(new \DateTime('12-12-2025 12:00:00'))
            ->setUrl('http://www.eventbrite.nl/e/registratie-19-vrije-editie-zondag-18482884806')
            ->setDojo($this->getReference('user'));

        $this->getReference('user')->addDojo($dojo);

        $manager->persist($dojo);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}