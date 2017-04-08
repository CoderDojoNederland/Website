<?php

use CoderDojo\WebsiteBundle\Command\ShipCocRequestCommand;

class ShipCocRequestCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_check_properties()
    {
        $command = new ShipCocRequestCommand('uuid-123');
        $this->assertSame('uuid-123', $command->getId());
    }
}