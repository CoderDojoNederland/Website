<?php

namespace CoderDojo\WebsiteBundle\Tests\Entity;

use CoderDojo\WebsiteBundle\Entity\User;
use CoderDojo\WebsiteBundle\Entity\DojoEvent;

/**
 * @covers \CoderDojo\WebsiteBundle\Entity\DojoEvent
 * @group FullCoverage
 */
class DojoEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|User
     */
    private $dojo;

    /**
     * @var DojoEvent
     */
    private $dojoEvent;

    public function setUp()
    {
        $this->dojo = $this->createDojoMock();

        $this->dojoEvent = new DojoEvent();

        $this->dojoEvent
            ->setUser($this->dojo)
            ->setUrl('url')
            ->setDate(new \DateTime('2016-01-01 00:00:00'))
            ->setName('dojo');
    }

    /**
     * @test
     */
    public function it_should_return_constructor_data()
    {
        $this->assertSame('url', $this->dojoEvent->getUrl());
        $this->assertSame('dojo', $this->dojoEvent->getName());
        $this->assertSame($this->dojo, $this->dojoEvent->getUser());
        $this->assertNull($this->dojoEvent->getId());
        $this->assertSame(\DateTime::class, get_class($this->dojoEvent->getDate()));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|User
     */
    private function createDojoMock()
    {
        return $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}