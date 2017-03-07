<?php

use CoderDojo\WebsiteBundle\Command\CreateEventCommand;
use CoderDojo\WebsiteBundle\Entity\DojoEvent;

class CreateEventCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_check_properties()
    {
        $date = new \DateTime('2020-01-01');

        $command = new CreateEventCommand(
            'uuid-123',
            'event name',
            $date,
            'http://event.url',
            null,
            DojoEvent::TYPE_CUSTOM
        );

        $this->assertSame('uuid-123', $command->getDojoId());
        $this->assertSame('event name', $command->getName());
        $this->assertSame($date, $command->getDate());
        $this->assertSame('http://event.url', $command->getUrl());
        $this->assertNull($command->getZenId());
        $this->assertSame(DojoEvent::TYPE_CUSTOM, $command->getType());
    }
}