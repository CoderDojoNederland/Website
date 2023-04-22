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
    private $confirmed = true;

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
