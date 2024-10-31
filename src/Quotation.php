<?php

namespace JM\Lalamove;

/**
 * Handles creation and retrieval of quotations through the Lalamove API.
 */
class Quotation
{
    /**
     * @var LalamoveClient Client instance to make API requests.
     */
    private $client;

    /**
     * Constructs a new Quotation instance.
     *
     * @param LalamoveClient $client The Lalamove client used to interact with the API.
     */
    public function __construct(LalamoveClient $client)
    {
        $this->client = $client;
    }

    /**
     * Creates a new quotation using the Lalamove API.
     *
     * @param array $payload The data payload for the quotation.
     * @return mixed The API response.
     */
    public function create(mixed $payload)
    {
        $path = '/v3/quotations';
        $body = json_encode(["data" => $payload]); // Encode the payload to JSON string
        $headers = $this->client->getSignatureGenerator()->getHeaders('POST', $path, $this->client->getMarket(), $body, $this->client->getRequestId());

        return $this->client->makeRequest('POST', $path, $headers, $body);
    }

    /**
     * Retrieves an existing quotation from the Lalamove API.
     *
     * @param string $quotationId The unique identifier for the quotation to retrieve.
     * @return mixed The API response.
     */
    public function retrieve(string $quotationId)
    {
        $path = "/v3/quotations/{$quotationId}";
        $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path, $this->client->getMarket(), '', $this->client->getRequestId());

        return $this->client->makeRequest('GET', $path, $headers, '');
    }
}



// namespace JMusthakeem\Lalamove;

// class Quotation
// {
//     private $client;

//     public function __construct(LalamoveClient $client)
//     {
//         $this->client = $client;
//     }


//     public function create($payload)
//     {
//         $path = '/v3/quotations';
//         $body = (["data" => $payload]);
//         $headers = $this->client->getSignatureGenerator()->getHeaders('POST', $path, $this->client->getMarket(), json_encode($body), $this->client->getRequestId());

//         return $this->client->makeRequest('POST', $path, $headers, json_encode($body));

//     }

//     public function retrieve($quotationId)
//     {
//         $path = "/v3/quotations/{$quotationId}";
//         $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path, $this->client->getMarket(), '', $this->client->getRequestId());

//         return $this->client->makeRequest('GET', $path, $headers, '');

//     }
    
// }
