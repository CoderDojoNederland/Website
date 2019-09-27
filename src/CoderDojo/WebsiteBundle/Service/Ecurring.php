<?php

declare(strict_types=1);

namespace CoderDojo\WebsiteBundle\Service;

use CoderDojo\WebsiteBundle\Entity\Club100;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Ecurring
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var int
     */
    private $subscriptionPlanQuarterly;

    /**
     * @var int
     */
    private $subscriptionPlanSemiYearly;

    /**
     * @var int
     */
    private $subscriptionPlanYearly;

    /**
     * @param Client                $client
     * @param UrlGeneratorInterface $router
     * @param int                   $subscriptionPlanQuarterly
     * @param int                   $subscriptionPlanSemiYearly
     * @param int                   $subscriptionPlanYearly
     */
    public function __construct(
        Client $client,
        UrlGeneratorInterface $router,
        int $subscriptionPlanQuarterly,
        int $subscriptionPlanSemiYearly,
        int $subscriptionPlanYearly
    ) {
        $this->client = $client;
        $this->router = $router;
        $this->subscriptionPlanQuarterly = $subscriptionPlanQuarterly;
        $this->subscriptionPlanSemiYearly = $subscriptionPlanSemiYearly;
        $this->subscriptionPlanYearly = $subscriptionPlanYearly;
    }

    /**
     * @param Club100 $member
     *
     * @return int The ecurring customer ID
     */
    public function createCustomer(Club100 $member): int
    {
        $request = new Request(
            'POST',
            '/customers',
            [],
            json_encode([
                'data' => [
                    'type' => 'customer',
                    'attributes' =>  [
                        'first_name' => $member->getFirstName(),
                        'last_name' => $member->getLastName(),
                        'email' => $member->getEmail()
                    ]
                ]
            ])
        );

        $response = json_decode($this->client->send($request)->getBody()->getContents(), true);

        return (int) $response['data']['id'];
    }

    /**
     * @param int    $customerID
     * @param string $subscriptionType
     * @param string $memberHash
     *
     * @return string The confirmation page link
     */
    public function createSubscription(int $customerID, string $subscriptionType, string $memberHash): string
    {
        switch ($subscriptionType) {
            case Club100::INTERVAL_QUARTERLY:
                $planId = $this->subscriptionPlanQuarterly;
                break;
            case Club100::INTERVAL_SEMI_YEARLY:
                $planId = $this->subscriptionPlanSemiYearly;
                break;
            case Club100::INTERVAL_YEARLY:
                $planId = $this->subscriptionPlanYearly;
                break;
            default:
                throw new \LogicException('Unsupported interval');
        }

        $request = new Request(
            'POST',
            '/subscriptions',
            [],
            json_encode([
                'data' => [
                    'type' => 'subscription',
                    'attributes' =>  [
                        'customer_id' => $customerID,
                        'subscription_plan_id' => $planId,
                        'confirmation_sent' => true,
                        'success_redirect_url' => $this->router->generate('club_of_100_confirm', ['hash' => $memberHash], UrlGeneratorInterface::ABSOLUTE_URL)
                    ]
                ]
            ])
        );

        $response = json_decode($this->client->send($request)->getBody()->getContents(), true);

        return $response['data']['attributes']['confirmation_page'];
    }
}
