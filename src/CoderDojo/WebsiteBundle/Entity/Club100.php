<?php

declare(strict_types=1);

namespace CoderDojo\WebsiteBundle\Entity;

use Assert\Assertion;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Table(name="club_100")
 * @ORM\Entity(repositoryClass="CoderDojo\WebsiteBundle\Repository\Club100Repository")
 */
class Club100
{
    public const INTERVAL_YEARLY_INVOICED = 'yearly-invoiced';
    public const INTERVAL_YEARLY = 'yearly';
    public const INTERVAL_SEMI_YEARLY = 'semi-yearly';
    public const INTERVAL_QUARTERLY = 'quarterly';
    public const INTERVALS = [
        self::INTERVAL_YEARLY_INVOICED,
        self::INTERVAL_YEARLY,
        self::INTERVAL_SEMI_YEARLY,
        self::INTERVAL_QUARTERLY
    ];

    public const REASON_NO_PAYMENT = 'no-payment-received';
    public const REASON_EXPENSIVE = 'expensive';
    public const REASON_OTHER = 'other';

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
     * @ORM\Column(type="string")
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $lastName;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $hash;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     **/
    private $twitter;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     **/
    private $company;

    /**
     * @var string
     * @ORM\Column(type="string")
     **/
    private $memberType;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $confirmed = false;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $reason;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $public;

    /**
     * @var string
     * @ORM\Column(type="string", name="payment_interval")
     */
    private $interval;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     **/
    private $avatar;

    /**
     * @var Donation[]|Collection
     * @ORM\OneToMany(targetEntity="CoderDojo\WebsiteBundle\Entity\Donation", mappedBy="member")
     **/
    private $donations;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     **/
    private $confirmationUrl;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     **/
    private $unsubscribedAt;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     **/
    private $unsubscribeReason;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->donations = new ArrayCollection();
        $this->hash = sha1(Uuid::uuid4()->toString());
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

    /**
     * @return bool
     */
    public function getPublic(): bool
    {
        return $this->public;
    }

    /**
     * @param bool $public
     */
    public function setPublic(bool $public): void
    {
        $this->public = $public;
    }

    /**
     * @return string
     */
    public function getInterval(): string
    {
        return $this->interval;
    }

    /**
     * @param string $interval
     */
    public function setInterval(string $interval): void
    {
        Assertion::inArray($interval, self::INTERVALS);

        $this->interval = $interval;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    /**
     * @param bool $confirmed
     */
    public function setConfirmed(bool $confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @return string|null
     */
    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    /**
     * @param string|null $twitter
     */
    public function setTwitter(?string $twitter): void
    {
        $this->twitter = $twitter;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string|null $company
     */
    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getMemberType(): string
    {
        return $this->memberType;
    }

    /**
     * @param string $memberType
     */
    public function setMemberType(string $memberType): void
    {
        $this->memberType = $memberType;
    }

    /**
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @param string|null $avatar
     */
    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return Donation[]|Collection
     */
    public function getDonations()
    {
        return $this->donations;
    }

    /**
     * @param Donation $donation
     */
    public function addDonation(Donation $donation): void
    {
        $this->donations->add($donation);
    }

    /**
     * @return string|null
     */
    public function getConfirmationUrl():? string
    {
        return $this->confirmationUrl;
    }

    /**
     * @param string $confirmationUrl
     */
    public function setConfirmationUrl(string $confirmationUrl): void
    {
        $this->confirmationUrl = $confirmationUrl;
    }

    /**
     * @param string $unsubscribeReason
     */
    public function unsubscribe(string $unsubscribeReason): void
    {
        $this->unsubscribedAt = new \DateTime;
        $this->unsubscribeReason = $unsubscribeReason;
    }

    /**
     * @return \DateTime
     */
    public function getUnsubscribedAt(): \DateTime
    {
        return $this->unsubscribedAt;
    }

    /**
     * @return bool
     */
    public function isUnsubscribed(): bool
    {
        return $this->unsubscribedAt !== null;
    }
}
