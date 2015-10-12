<?php

namespace TicketSwap\Core\UploadBundle\Tests\Entity;
use Coderdojo\WebsiteBundle\Entity\Dojo;
use Coderdojo\WebsiteBundle\Entity\DojoEvent;

/**
 * @covers \Coderdojo\WebsiteBundle\Entity\DojoEvent
 * @group FullCoverage
 */
class DojoEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Dojo
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
            ->setDojo($this->dojo)
            ->setUrl('url')
            ->setDojodate(new \DateTime('2016-01-01 00:00:00'))
            ->setName('dojo');
    }

    /**
     * @test
     */
    public function it_should_return_constructor_data()
    {
        $this->assertSame('url', $this->dojoEvent->getUrl());
        $this->assertSame('dojo', $this->dojoEvent->getName());
        $this->assertSame($this->dojo, $this->dojoEvent->getDojo());
        $this->assertNull($this->dojoEvent->getId());
        $this->assertSame(\DateTime::class, get_class($this->dojoEvent->getDojodate()));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Dojo
     */
    private function createDojoMock()
    {
        return $this->getMockBuilder(Dojo::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}