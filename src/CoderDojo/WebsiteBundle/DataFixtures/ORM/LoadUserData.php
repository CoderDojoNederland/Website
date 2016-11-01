<?php

namespace CoderDojo\WebsiteBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CoderDojo\WebsiteBundle\Entity\User;

/**
 * Class LoadUserData
 * @codeCoverageIgnore
 */
class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setUsername('contact@coderdojo-city.nl')
            ->setEmail('contact@coderdojo-city.nl');
        $user
            ->setLocation('Startup Port')
            ->setCity('Rotterdam')
            ->setStreet('Heer Bokelweg')
            ->setHousenumber('155')
            ->setName('CoderDojo City')
            ->setFacebook('http://facebook.com/coderdojonederland')
            ->setTwitter('http://twitter.com/coderdojonl')
            ->setWebsite('http://coderdojo.nl')
            ->setPostalcode('3032AD')
            ->setOrganiser('5495554471')
            ->setEnabled(true)
            ->setLat(51.928294)
            ->setLong(4.480246);

        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user);
        $user->setPassword($encoder->encodePassword('coderdojo', $user->getSalt()));

        $manager->persist($user);
        $manager->flush();

        $this->addReference('user', $user);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
