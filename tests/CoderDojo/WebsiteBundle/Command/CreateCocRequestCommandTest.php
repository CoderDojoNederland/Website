<?php

use CoderDojo\WebsiteBundle\Command\CreateCocRequestCommand;

class CreateCocRequestCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_check_properties()
    {
        $command = new CreateCocRequestCommand(
            'uuid-123',
            'CJ',
            'Vermeulen',
            'christian@email.com',
            'some notes',
            1,
            'uuid-123'
        );

        $this->assertSame('uuid-123', $command->getId());
        $this->assertSame('CJ', $command->getLetters());
        $this->assertSame('Vermeulen', $command->getName());
        $this->assertSame('christian@email.com', $command->getEmail());
        $this->assertSame('some notes', $command->getNotes());
        $this->assertSame(1, $command->getUserId());
        $this->assertSame('uuid-123', $command->getDojoId());
    }
}