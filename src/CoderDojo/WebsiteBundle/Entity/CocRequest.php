<?php

namespace CoderDojo\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="email_uidx", columns={"email"})})
 */
class CocRequest
{
    const STATUS_CREATED = 'created';
    const STATUS_PREPARED = 'prepared';
    const STATUS_REQUESTED = 'requested';
    const STATUS_EXPIRED = 'expired';
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
     *
     * @ORM\Column(type="string")
     */
    private $letters;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     **/
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     **/
    private $preparedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     **/
    private $expiresAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     **/
    private $requestedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     **/
    private $receivedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     **/
    private $expiryReminderSent;

    /**
     * CocRequest constructor.
     * @param string $id
     * @param string $letters
     * @param string $name
     * @param string $email
     * @param string|null $notes
     * @param User $requestedBy
     * @param Dojo $requestedFor
     */
    public function __construct(
        $id,
        $letters,
        $name,
        $email,
        $notes=null,
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
        $this->createdAt = new \DateTime();
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getPreparedAt()
    {
        return $this->preparedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @return \DateTime
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }

    /**
     * @return \DateTime
     */
    public function getReceivedAt()
    {
        return $this->receivedAt;
    }

    /**
     * @return string|null
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return bool
     */
    public function isExpiryReminderSent(): bool
    {
        return $this->expiryReminderSent;
    }

    public function expiryReminderSent(): void
    {
        $this->expiryReminderSent = true;
    }

    public function prepared()
    {
        $this->preparedAt = new \DateTime();
        $this->expiresAt = new \DateTime('+ 30 days');
        $this->expiryReminderSent = false;
        $this->status = self::STATUS_PREPARED;
    }

    public function requested()
    {
        if (null !== $this->requestedAt) {
            throw new \Exception('This COC has already been requested');
        }

        $this->requestedAt = new \DateTime();
        $this->expiresAt = null;
        $this->status = self::STATUS_REQUESTED;
    }

    public function received()
    {
        if (null !== $this->receivedAt) {
            throw new \Exception('This COC has already been received');
        }

        $this->receivedAt = new \DateTime();
        $this->status = self::STATUS_RECEIVED;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
