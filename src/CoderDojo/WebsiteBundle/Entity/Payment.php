<?php

declare(strict_types=1);

namespace CoderDojo\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="club_100_payment")
 * @ORM\Entity()
 */
class Payment
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
     * @ORM\Column(type="string")
     **/
    private $mollieId;

    /**
     * @var string
     * @ORM\Column(type="string")
     **/
    private $status;

    /**
     * @var string
     * @ORM\Column(type="string")
     **/
    private $checkoutUrl;

    /**
     * @var Donation
     * @ORM\OneToOne(targetEntity="CoderDojo\WebsiteBundle\Entity\Donation")
     * @ORM\JoinColumn(referencedColumnName="id", name="donation_id")
     **/
    private $donation;

    /**
     * @param Donation $donation
     * @param string   $mollieId
     * @param string   $status
     * @param string   $checkoutUrl
     */
    public function __construct(Donation $donation, string $mollieId, string $status, string $checkoutUrl)
    {
        $this->mollieId    = $mollieId;
        $this->status      = $status;
        $this->checkoutUrl = $checkoutUrl;
        $this->donation    = $donation;
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
    public function getMollieId(): string
    {
        return $this->mollieId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getCheckoutUrl(): string
    {
        return $this->checkoutUrl;
    }

    /**
     * @return Donation
     */
    public function getDonation(): Donation
    {
        return $this->donation;
    }
}
