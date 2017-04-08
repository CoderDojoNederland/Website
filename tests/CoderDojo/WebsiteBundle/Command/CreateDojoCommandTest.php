<?php

use CoderDojo\WebsiteBundle\Command\CreateDojoCommand;

class CreateDojoCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_check_properties()
    {
        $command = new CreateDojoCommand(
            'uuid-123',
            'creator@email.com',
            'https://zen.url',
            'Dojo Name',
            'city',
            3.4,
            4.3,
            'dojo@email.com',
            'https://dojo.nl',
            'mydojo',
            false
        );

        $this->assertSame('uuid-123', $command->getZenId());
        $this->assertSame('creator@email.com', $command->getZenCreatorEmail());
        $this->assertSame('https://zen.url', $command->getZenUrl());
        $this->assertSame('Dojo Name', $command->getName());
        $this->assertSame('city', $command->getCity());
        $this->assertSame(3.4, $command->getLat());
        $this->assertSame(4.3, $command->getLon());
        $this->assertSame('dojo@email.com', $command->getEmail());
        $this->assertSame('https://dojo.nl', $command->getWebsite());
        $this->assertSame('mydojo', $command->getTwitter());
        $this->assertFalse($command->isRemoved());
    }

    /**
     * @test
     */
    public function it_should_default_url()
    {
        $command = new CreateDojoCommand(
            'uuid-123',
            'creator@email.com',
            'https://zen.url',
            'Dojo Name',
            'city',
            3.4,
            4.3,
            'dojo@email.com',
            null,
            'mydojo',
            false
        );

        $this->assertSame('https://coderdojo.nl', $command->getWebsite());
    }

    /**
     * @test
     */
    public function it_should_clean_twitter()
    {
        $command = new CreateDojoCommand(
            'uuid-123',
            'creator@email.com',
            'https://zen.url',
            'Dojo Name',
            'city',
            3.4,
            4.3,
            'dojo@email.com',
            null,
            '@mydojo',
            false
        );

        $this->assertSame('mydojo', $command->getTwitter());

        $command = new CreateDojoCommand(
            'uuid-123',
            'creator@email.com',
            'https://zen.url',
            'Dojo Name',
            'city',
            3.4,
            4.3,
            'dojo@email.com',
            null,
            'https://twitter.com/mydojo',
            false
        );

        $this->assertSame('mydojo', $command->getTwitter());

        $command = new CreateDojoCommand(
            'uuid-123',
            'creator@email.com',
            'https://zen.url',
            'Dojo Name',
            'city',
            3.4,
            4.3,
            'dojo@email.com',
            null,
            '',
            false
        );

        $this->assertSame('coderdojonl', $command->getTwitter());
    }
}