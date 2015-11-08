<?php

namespace Coderdojo\WebsiteBundle\Service;

use CL\Slack\Payload\ChatPostMessagePayload;
use CL\Slack\Payload\PayloadResponseInterface;
use CL\Slack\Transport\ApiClient;

class SlackService
{
    /**
     * @var ApiClient
     */
    private $client;

    /**
     * SlackService constructor.
     * @param ApiClient $client
     */
    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Send a message to a channel
     *
     * @param string $channel To which channel do you send
     * @param string $message What is your message
     * @return PayloadResponseInterface
     */
    public function sendToChannel($channel, $message)
    {
        $payload = new ChatPostMessagePayload();
        $payload->setChannel($channel);
        $payload->setText($message);
        $payload->setUsername('DojoBot');
        $payload->setIconEmoji('coderdojo');

        $response = $this->client->send($payload);

        return $response;
    }
}