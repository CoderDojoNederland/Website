<?php

use CoderDojo\WebsiteBundle\Command\RemoveDojoCommand;

class RemoveDojoCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_check_properties()
    {
        $command = new RemoveDojoCommand('uuid-123');
        $this->assertSame('uuid-123', $command->getId());
    }
}