<?php

use CoderDojo\WebsiteBundle\Command\RemoveEventCommand;

class RemoveEventCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_check_properties()
    {
        $command = new RemoveEventCommand('uuid-123');
        $this->assertSame('uuid-123', $command->getId());
    }
}