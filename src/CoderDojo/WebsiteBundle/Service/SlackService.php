<?php

namespace CoderDojo\WebsiteBundle\Service;

use CL\Slack\Model\Attachment;
use CL\Slack\Payload\ChatPostMessagePayload;
use CL\Slack\Payload\PayloadResponseInterface;
use CL\Slack\Transport\ApiClient;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Kernel;

class SlackService
{
    /**
     * @var ApiClient
     */
    private $client;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * SlackService constructor.
     * @param $token
     * @param Kernel $kernel
     */
    public function __construct($token, Kernel $kernel)
    {
        $this->client = new ApiClient($token);
        $this->kernel = $kernel;
    }


    /**
     * @param string $channel
     * @param string $message
     * @param Attachment[] $attachments
     */
    public function sendToChannel($channel, $message, $attachments = [])
    {
        if ('prod' !== $this->kernel->getEnvironment()) {
            $channel = "#website-nl";
        }

        $payload = new ChatPostMessagePayload();
        $payload->setChannel($channel);
        $payload->setText($message);
        $payload->setUsername('DojoBot');
        $payload->setIconEmoji('coderdojo');

        if (false === empty($attachments)) {
            foreach ($attachments as $attachment) {
                $payload->addAttachment($attachment);
            }
        }

        $this->client->send($payload);

        return;
    }
}