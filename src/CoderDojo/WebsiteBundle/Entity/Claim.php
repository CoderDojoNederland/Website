<?php

namespace CoderDojo\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Ramsey\Uuid\Uuid;

/**
 * Claim
 *
 * @ORM\Table(name="Claim")
 * @ORM\Entity(repositoryClass="CoderDojo\WebsiteBundle\Repository\ClaimRepository")
 * @ExclusionPolicy("none")
 */
class Claim
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="hash", type="string")
     */
    private $hash;

    /**
     * @var Dojo
     * @ORM\ManyToOne(targetEntity="CoderDojo\WebsiteBundle\Entity\Dojo")
     * @ORM\JoinColumn(name="dojo_id", referencedColumnName="id")
     */
    private $dojo;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="CoderDojo\WebsiteBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \DateTime
     * @ORM\Column(name="expires", type="datetime")
     */
    private $expires;

    /**
     * @var \DateTime
     * @ORM\Column(name="claimed", type="datetime", nullable=true)
     */
    private $claimed = null;

    /**
     * Claim constructor.
     * @param Dojo $dojo
     * @param User $user
     */
    public function __construct(Dojo $dojo, User $user)
    {
        $this->dojo = $dojo;
        $this->user = $user;
        $this->expires = new \DateTime('+2 days');
        $this->hash = Uuid::uuid4()->toString();
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return Dojo
     */
    public function getDojo()
    {
        return $this->dojo;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \DateTime
     */
    public function expiresAt()
    {
        return $this->expires;
    }

    /**
     * @return bool
     */
    public function isExpired() {
        return new \DateTime() > $this->expiresAt();
    }

    /**
     * @return \DateTime
     */
    public function getClaimedAt()
    {
        return $this->claimed;
    }

    /**
     *
     */
    public function claim()
    {
        if (null !== $this->claimed) {
            throw new \LogicException('This has already been claimed!');
        }

        $this->claimed = new \DateTime();
    }
}