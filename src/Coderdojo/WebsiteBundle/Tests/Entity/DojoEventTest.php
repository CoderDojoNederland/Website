<?php

namespace TicketSwap\Core\UploadBundle\Tests\Entity;
use Coderdojo\WebsiteBundle\Entity\DojoEvent;

/**
 * @covers \Coderdojo\WebsiteBundle\Entity\DojoEvent
 * @group FullCoverage
 */
class DojoEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DojoEvent
     */
    private $dojoEvent;

    public function setUp()
    {
        $this->dojoEvent = new DojoEvent();
        $this->dojoEvent->setUrl('url')
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
    }
}