<?php

namespace JM\Lalamove;

/**
 * Manages retrieval of market-related information via the Lalamove API.
 */
class Market
{
    /**
     * @var LalamoveClient The client used to interact with the Lalamove API.
     */
    private $client;

    /**
     * Constructs a new Market instance.
     *
     * @param LalamoveClient $client The client instance to handle API requests.
     */
    public function __construct(LalamoveClient $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieves market information from the Lalamove API.
     * Optionally allows specification of a different market to query.
     *
     * @param string $reqMarket Optional market identifier to override the client's default market.
     * @return mixed The API response.
     */
    public function retrieve(string $reqMarket = '')
    {
        $path = "/v3/cities";
        $market = $reqMarket ?: $this->client->getMarket(); // Use provided market or default to client's market.
        
        // Validate market
        if (empty(trim($market))) {
            throw new \InvalidArgumentException('Market cannot be empty');
        }
        
        $headers = $this->getHeaders('GET', $path, $market);
        return $this->client->makeRequest('GET', $path, $headers);
    }
    
    /**
     * Helper method to get request headers with proper authorization.
     *
     * @param string $method The HTTP method.
     * @param string $path The API endpoint path.
     * @param string $market The market to use for this request.
     * @param string $body The request body (if applicable).
     * @return array The prepared headers.
     */
    private function getHeaders(string $method, string $path, string $market, string $body = ''): array
    {
        return $this->client->getSignatureGenerator()->getHeaders(
            $method,
            $path,
            $market,
            $body,
            $this->client->getRequestId()
        );
    }
}
