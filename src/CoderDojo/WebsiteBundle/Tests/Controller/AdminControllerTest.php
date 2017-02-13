<?php

namespace CoderDojo\WebsiteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testListvog()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/vog');
    }

}
