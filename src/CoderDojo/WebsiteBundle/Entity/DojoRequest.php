<?php

namespace CoderDojo\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * Dojo
 *
 * @ORM\Table(name="DojoRequest")
 * @ORM\Entity(repositoryClass="CoderDojo\WebsiteBundle\Repository\DojoRequestRepository")
 * @ExclusionPolicy("none")
 */
class DojoRequest
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
     * @var Dojo
     *
     * @ORM\ManyToOne(targetEntity="CoderDojo\WebsiteBundle\Entity\Dojo", inversedBy="mentorRequests")
     * @ORM\JoinColumn(name="dojo_id", referencedColumnName="id")
     */
    private $dojo;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="CoderDojo\WebsiteBundle\Entity\User", inversedBy="requests")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \DateTime
     * @ORM\Column(name="requested", type="datetime")
     */
    private $requested;

    /**
     * @var \DateTime
     * @ORM\Column(name="approved", type="datetime", nullable=true)
     */
    private $approved = null;

    /**
     * DojoRequest constructor.
     * @param Dojo $dojo
     * @param User $user
     */
    public function __construct(Dojo $dojo, User $user)
    {
        $this->dojo = $dojo;
        $this->user = $user;
        $this->requested = new \DateTime();
    }

    public function setApproved()
    {
        if (null !== $this->approved) {
            throw new \Exception('request already approved');
        }

        $this->approved = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
    public function getRequested()
    {
        return $this->requested;
    }

    /**
     * @return \DateTime
     */
    public function getApproved()
    {
        return $this->approved;
    }

}