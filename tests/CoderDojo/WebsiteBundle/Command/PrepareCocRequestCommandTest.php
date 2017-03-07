<?php

use CoderDojo\WebsiteBundle\Command\PrepareCocRequestCommand;

class PrepareCocRequestCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_check_properties()
    {
        $command = new PrepareCocRequestCommand('uuid-123');
        $this->assertSame('uuid-123', $command->getId());
    }
}