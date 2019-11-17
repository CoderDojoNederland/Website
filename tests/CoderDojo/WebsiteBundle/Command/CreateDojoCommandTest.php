<?php

use CoderDojo\WebsiteBundle\Command\CreateDojoCommand;

class CreateDojoCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_check_properties()
    {
        $verifiedAt = (new DateTime('-2 weeks'))->format(DATE_ATOM);
        $command    = new CreateDojoCommand(
            'uuid-123',
            $verifiedAt,
            'creator@email.com',
            'https://zen.url',
            'Dojo Name',
            'city',
            3.4,
            4.3,
            'dojo@email.com',
            'https://dojo.nl',
            'mydojo',
            'NL',
            false
        );

        $this->assertSame('uuid-123', $command->getZenId());
        $this->assertSame($verifiedAt, $command->getVerifiedAt());
        $this->assertSame('creator@email.com', $command->getZenCreatorEmail());
        $this->assertSame('https://zen.url', $command->getZenUrl());
        $this->assertSame('Dojo Name', $command->getName());
        $this->assertSame('city', $command->getCity());
        $this->assertSame(3.4, $command->getLat());
        $this->assertSame(4.3, $command->getLon());
        $this->assertSame('dojo@email.com', $command->getEmail());
        $this->assertSame('https://dojo.nl', $command->getWebsite());
        $this->assertSame('mydojo', $command->getTwitter());
        $this->assertSame('NL', $command->getCountry());
        $this->assertFalse($command->isRemoved());
    }

    /**
     * @test
     */
    public function it_should_default_url()
    {
        $verifiedAt = (new DateTime('-2 weeks'))->format(DATE_ATOM);
        $command = new CreateDojoCommand(
            'uuid-123',
            $verifiedAt,
            'creator@email.com',
            'https://zen.url',
            'Dojo Name',
            'city',
            3.4,
            4.3,
            'dojo@email.com',
            null,
            'mydojo',
            'NL',
            false
        );

        $this->assertSame('https://coderdojo.nl', $command->getWebsite());
    }

    /**
     * @test
     */
    public function it_should_clean_twitter()
    {
        $verifiedAt = (new DateTime('-2 weeks'))->format(DATE_ATOM);
        $command = new CreateDojoCommand(
            'uuid-123',
            $verifiedAt,
            'creator@email.com',
            'https://zen.url',
            'Dojo Name',
            'city',
            3.4,
            4.3,
            'dojo@email.com',
            null,
            '@mydojo',
            'NL',
            false
        );

        $this->assertSame('mydojo', $command->getTwitter());

        $command = new CreateDojoCommand(
            'uuid-123',
            $verifiedAt,
            'creator@email.com',
            'https://zen.url',
            'Dojo Name',
            'city',
            3.4,
            4.3,
            'dojo@email.com',
            null,
            'https://twitter.com/mydojo',
            'NL',
            false
        );

        $this->assertSame('mydojo', $command->getTwitter());

        $command = new CreateDojoCommand(
            'uuid-123',
            $verifiedAt,
            'creator@email.com',
            'https://zen.url',
            'Dojo Name',
            'city',
            3.4,
            4.3,
            'dojo@email.com',
            null,
            '',
            'NL',
            false
        );

        $this->assertSame('coderdojonl', $command->getTwitter());
    }
}
