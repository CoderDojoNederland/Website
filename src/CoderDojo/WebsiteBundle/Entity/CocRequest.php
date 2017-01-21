<?php

namespace CoderDojo\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class CocRequest
{
    const STATUS_CREATED = 'created';
    const STATUS_REQUESTED = 'requested';
    const STATUS_RECEIVED = 'received';

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="string")
     */
    private $id;

    /**
     * @var string
     */
    private $letters;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $notes;

    /**
     * @var string
     **/
    private $status;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="CoderDojo\WebsiteBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $requestedBy;

    /**
     * @var Dojo
     *
     * @ORM\ManyToOne(targetEntity="CoderDojo\WebsiteBundle\Entity\Dojo")
     * @ORM\JoinColumn(name="dojo_id", referencedColumnName="id")
     */
    private $requestedFor;

    /**
     * @var \DateTimeImmutable
     **/
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     **/
    private $requestedAt;

    /**
     * @var \DateTimeImmutable
     **/
    private $receivedAt;

    /**
     * CocRequest constructor.
     * @param string $id
     * @param string $letters
     * @param string $name
     * @param string $email
     * @param string $notes
     * @param User   $requestedBy
     * @param Dojo   $requestedFor
     */
    public function __construct(
        $id,
        $letters,
        $name,
        $email,
        $notes,
        User $requestedBy,
        Dojo $requestedFor
    ) {
        $this->id = $id;
        $this->letters = $letters;
        $this->name = $name;
        $this->email = $email;
        $this->notes = $notes;
        $this->status = self::STATUS_CREATED;
        $this->requestedBy = $requestedBy;
        $this->requestedFor = $requestedFor;
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLetters()
    {
        return $this->letters;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return User
     */
    public function getRequestedBy()
    {
        return $this->requestedBy;
    }

    /**
     * @return Dojo
     */
    public function getRequestedFor()
    {
        return $this->requestedFor;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getReceivedAt()
    {
        return $this->receivedAt;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    public function requested()
    {
        if (null !== $this->requestedAt) {
            throw new \Exception('This COC has already been requested');
        }

        $this->requestedAt = new \DateTimeImmutable();
        $this->status = self::STATUS_REQUESTED;
    }

    public function received()
    {
        if (null !== $this->receivedAt) {
            throw new \Exception('This COC has already been received');
        }

        $this->receivedAt = new \DateTimeImmutable();
        $this->status = self::STATUS_RECEIVED;
    }
}