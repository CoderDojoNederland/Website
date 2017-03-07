<?php

use Assert\Assertion;
use CoderDojo\WebsiteBundle\Entity\Claim;
use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Entity\User;

class ClaimTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_check_properties()
    {
        $dojo = $this->prophesize(Dojo::class);
        $user = $this->prophesize(User::class);

        $claim = new Claim($dojo->reveal(), $user->reveal());

        $this->assertSame($dojo->reveal(), $claim->getDojo());
        $this->assertSame($user->reveal(), $claim->getUser());
        $this->assertNull($claim->getClaimedAt());
        $this->assertFalse($claim->isExpired());
        Assertion::uuid($claim->getHash());

        $claim->claim();
        $this->assertInstanceOf(\DateTime::class, $claim->getClaimedAt());
    }
}
