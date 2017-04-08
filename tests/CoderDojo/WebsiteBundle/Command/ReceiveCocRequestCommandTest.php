<?php

use CoderDojo\WebsiteBundle\Command\ReceiveCocRequestCommand;

class ReceiveCocRequestCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_check_properties()
    {
        $command = new ReceiveCocRequestCommand('uuid-123');
        $this->assertSame('uuid-123', $command->getId());
    }
}