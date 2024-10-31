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
        // Debugging or logging statements should typically be handled through logging services rather than echoing directly.
        // Example: $this->logger->info("Rescue: " . $this->client->isJSONResponse());
        // Example: $this->logger->info($this->client->getRequestId() . " *** reQQ3 ***");

        $path = "/v3/cities";
        $market = $reqMarket ?: $this->client->getMarket(); // Use provided market or default to client's market.
        $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path, $market, '', $this->client->getRequestId());

        return $this->client->makeRequest('GET', $path, $headers, '');
    }
}


// namespace JMusthakeem\Lalamove;

// class Market
// {
//     private $client;

//     public function __construct(LalamoveClient $client)
//     {
//         $this->client = $client;
//     }

//      public function retrieve($reqMarket = '')
//     {
//         echo "Rescue: " .$this->client->isJSONResponse() ;
//         echo $this->client->getRequestId(). "*** reQQ3 ***\n";

//         $path = "/v3/cities";
//         $market = $reqMarket ?: $this->client->getMarket();
//         $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path, $market, '', $this->client->getRequestId());

//         return $this->client->makeRequest('GET', $path, $headers, '');

//     }
// }
