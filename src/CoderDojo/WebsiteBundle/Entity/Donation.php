<?php

declare(strict_types=1);

namespace CoderDojo\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Table(name="club_100_donation")
 * @ORM\Entity()
 */
class Donation
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
     * @var Club100
     * @ORM\ManyToOne(targetEntity="CoderDojo\WebsiteBundle\Entity\Club100", inversedBy="donations")
     * @ORM\JoinColumn(referencedColumnName="id", name="member_id", nullable=false)
     */
    private $member;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quarter;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     **/
    private $paidAt;

    /**
     * @var Payment
     * @ORM\OneToOne(targetEntity="CoderDojo\WebsiteBundle\Entity\Payment")
     * @ORM\JoinColumn(referencedColumnName="id", name="payment_id", nullable=true)
     **/
    private $payment;

    /**
     * @var string
     * @ORM\Column(type="string")
     **/
    private $uuid;

    /**
     * @param Club100 $member
     */
    public function __construct(Club100 $member)
    {
        $year = (int) (new \DateTime())->format('Y');
        $quarter = $member->getInterval() === 'yearly' ? null : (int) ceil((new \DateTime())->format('m') / 3);
        $this->member = $member;
        $this->year = $year;
        $this->quarter = $quarter;
        $this->uuid = Uuid::uuid4()->toString();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Club100
     */
    public function getMember(): Club100
    {
        return $this->member;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @return int|null
     */
    public function getQuarter(): ?int
    {
        return $this->quarter;
    }

    /**
     * @return \DateTime
     */
    public function getPaidAt(): \DateTime
    {
        return $this->paidAt;
    }

    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->paidAt !== null;
    }

    /**
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }

    /**
     * @param Payment $payment
     */
    public function setPayment(Payment $payment): void
    {
        $this->payment = $payment;
        $this->paidAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }
}
