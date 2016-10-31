<?php

namespace Coderdojo\WebsiteBundle\Tests\Entity;

use Coderdojo\WebsiteBundle\Entity\User;
use Coderdojo\WebsiteBundle\Entity\DojoEvent;

/**
 * @covers \Coderdojo\WebsiteBundle\Entity\User
 * @group FullCoverage
 */
class DojoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var User
     */
    private $dojo;

    public function setUp()
    {
        $this->dojo = new User();
        $this->dojo
            ->setName('chris')
            ->setStreet('street')
            ->setHousenumber('10')
            ->setPostalcode('1234aa')
            ->setCity('city')
            ->setFacebook('facebook')
            ->setTwitter('twitter')
            ->setWebsite('website')
            ->setLocation('location')
            ->setOrganiser('organiser');
    }

    /**
     * @test
     */
    public function it_should_return_constructor_data()
    {
        $event1 = $this->createDojoEventMock();
        $event2 = $this->createDojoEventMock();

        $this->dojo->addDojo($event1);
        $this->dojo->addDojo($event2);

        $this->assertSame('chris', $this->dojo->getName());
        $this->assertSame('street', $this->dojo->getStreet());
        $this->assertSame('10', $this->dojo->getHousenumber());
        $this->assertSame('1234aa', $this->dojo->getPostalcode());
        $this->assertSame('city', $this->dojo->getCity());
        $this->assertSame('facebook', $this->dojo->getFacebook());
        $this->assertSame('twitter', $this->dojo->getTwitter());
        $this->assertSame('website', $this->dojo->getWebsite());
        $this->assertSame('location', $this->dojo->getLocation());
        $this->assertSame('organiser', $this->dojo->getOrganiser());
        $this->assertCount(2, $this->dojo->getDojos());
        $this->dojo->removeDojo($event1);
        $this->assertCount(1, $this->dojo->getDojos());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DojoEvent
     */
    private function createDojoEventMock()
    {
        return $this->getMockBuilder(DojoEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}