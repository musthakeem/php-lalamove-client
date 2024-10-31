<?php

namespace JM\Lalamove;

/**
 * Manages webhooks for Lalamove API interactions.
 */
class Webhook
{
    /**
     * @var LalamoveClient The Lalamove client instance to interact with the API.
     */
    private $client;

    /**
     * Constructor for the Webhook class.
     *
     * @param LalamoveClient $client The client object to use for HTTP requests.
     */
    public function __construct(LalamoveClient $client)
    {
        $this->client = $client;
    }

    /**
     * Sets a webhook URL for the Lalamove API.
     *
     * This method configures a webhook URL which the API will use to send event notifications.
     *
     * @param string $url The URL to which the Lalamove API should send notifications.
     * @return mixed The response from the Lalamove API.
     */
    public function setWebhook($url)
    {
        $path = '/v3/webhook';
        $body = json_encode(["data" => ["url" => $url]]);
        $headers = $this->client->getSignatureGenerator()->getHeaders('PATCH', $path, $this->client->getMarket(), $body, $this->client->getRequestId());

        return $this->client->makeRequest('PATCH', $path, $headers, $body);
    }
}

