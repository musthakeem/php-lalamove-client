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
     * @throws \InvalidArgumentException If the URL is invalid.
     */
    public function setWebhook(string $url)
    {
        // Validate the URL
        if (empty(trim($url)) || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid webhook URL provided');
        }

        $path = '/v3/webhook';
        $body = json_encode(["data" => ["url" => $url]]);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Error encoding webhook data: ' . json_last_error_msg());
        }
        
        $headers = $this->getHeaders('PATCH', $path, $body);
        return $this->client->makeRequest('PATCH', $path, $headers, $body);
    }
    
    /**
     * Helper method to get request headers with proper authorization.
     *
     * @param string $method The HTTP method.
     * @param string $path The API endpoint path.
     * @param string $body The request body (if applicable).
     * @return array The prepared headers.
     */
    private function getHeaders(string $method, string $path, string $body = ''): array
    {
        return $this->client->getSignatureGenerator()->getHeaders(
            $method,
            $path,
            $this->client->getMarket(),
            $body,
            $this->client->getRequestId()
        );
    }
}

