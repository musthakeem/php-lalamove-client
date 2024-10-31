<?php

namespace JM\Lalamove;

/**
 * Manages order operations including creation, retrieval, modification, and cancellation via the Lalamove API.
 */
class Order
{
    /**
     * @var LalamoveClient The client used to make API requests.
     */
    private $client;

    /**
     * Constructs a new Order instance.
     *
     * @param LalamoveClient $client The Lalamove client used to interact with the API.
     */
    public function __construct(LalamoveClient $client)
    {
        $this->client = $client;
    }

    /**
     * Creates a new order using the provided payload.
     *
     * @param array $payload The data payload for the new order.
     * @return mixed The API response.
     */
    public function create(mixed $payload)
    {
        $path = '/v3/orders';
        $body = json_encode(["data" => $payload]);
        $headers = $this->client->getSignatureGenerator()->getHeaders('POST', $path, $this->client->getMarket(), $body, $this->client->getRequestId());

        return $this->client->makeRequest('POST', $path, $headers, $body);
    }

    /**
     * Retrieves an existing order by its ID.
     *
     * @param string $orderId The unique identifier for the order.
     * @return mixed The API response.
     */
    public function retrieve(string $orderId)
    {
        $path = "/v3/orders/{$orderId}";
        $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path, $this->client->getMarket(), '', $this->client->getRequestId());

        return $this->client->makeRequest('GET', $path, $headers, '');
    }

    /**
     * Cancels an order by its ID.
     *
     * @param string $orderId The unique identifier for the order to be cancelled.
     * @return mixed The API response.
     */
    public function cancel(string $orderId)
    {
        $path = "/v3/orders/{$orderId}";
        $headers = $this->client->getSignatureGenerator()->getHeaders('DELETE', $path, $this->client->getMarket(), '', $this->client->getRequestId());

        return $this->client->makeRequest('DELETE', $path, $headers, '');
    }

    /**
     * Edits an existing order by updating its payload.
     *
     * @param string $orderId The unique identifier for the order to be updated.
     * @param array $payload The data payload with the new order details.
     * @return mixed The API response.
     */
    public function edit(string $orderId, mixed $payload)
    {
        $path = "/v3/orders/{$orderId}";
        $body = json_encode(["data" => ['stops' => $payload]]);
        $headers = $this->client->getSignatureGenerator()->getHeaders('PATCH', $path, $this->client->getMarket(), $body, $this->client->getRequestId());

        return $this->client->makeRequest('PATCH', $path, $headers, $body);
    }
    
    /**
     * Adds a priority fee to an existing order.
     *
     * @param string $orderId The unique identifier for the order.
     * @param float $fee The amount of the priority fee to be added.
     * @return mixed The API response.
     */
    public function addPriorityFee(string $orderId, string $fee)
    {
        $path = "/v3/orders/{$orderId}/priority-fee";
        $body = json_encode(["data" => ["priorityFee"=> $fee]]);
        $headers = $this->client->getSignatureGenerator()->getHeaders('POST', $path, $this->client->getMarket(), $body, $this->client->getRequestId());

        return $this->client->makeRequest('POST', $path, $headers, $body);
    }
}



// namespace JMusthakeem\Lalamove;

// class Order
// {
//     private $client;

//     public function __construct(LalamoveClient $client)
//     {
//         $this->client = $client;
//     }

//     public function create($payload)
//     {
//         $path = '/v3/orders';
//         $body = (["data" => $payload]);
//         $headers = $this->client->getSignatureGenerator()->getHeaders('POST', $path, $this->client->getMarket(), json_encode($body), $this->client->getRequestId());

//         return $this->client->makeRequest('POST', $path, $headers, json_encode($body), $this->client->isJSONResponse());

//     }

//     public function retrieve($orderId)
//     {
//         $path = "/v3/orders/{$orderId}";
//         $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path, $this->client->getMarket(), '', $this->client->getRequestId());

//         return $this->client->makeRequest('GET', $path, $headers, '', $this->client->isJSONResponse());
//     }

//     public function cancel($orderId)
//     {
//         $path = "/v3/orders/{$orderId}";
//         $headers = $this->client->getSignatureGenerator()->getHeaders('DELETE', $path, $this->client->getMarket(), '', $this->client->getRequestId());

//         return $this->client->makeRequest('DELETE', $path, $headers, '', $this->client->isJSONResponse());
//     }

//     public function edit($orderId, $payload)
//     {
//         $path = "/v3/orders/{$orderId}";
//         $body = (["data" => ['stops' => $payload]]);
//         print_r(json_encode($body,JSON_PRETTY_PRINT));
//         $headers = $this->client->getSignatureGenerator()->getHeaders('PATCH', $path, $this->client->getMarket(), json_encode($body), $this->client->getRequestId());

//         return $this->client->makeRequest('PATCH', $path, $headers, json_encode($body), $this->client->isJSONResponse());
//     }
    
//     public function addPriorityFee($orderId, $fee)
//     {
//         $path = "/v3/orders/{$orderId}/priority-fee";
//         $payload = (["priorityFee"=> $fee]);
//         $body = (["data" => $payload]);
//         $headers = $this->client->getSignatureGenerator()->getHeaders('POST', $path, $this->client->getMarket(), json_encode($body), $this->client->getRequestId());

//         return $this->client->makeRequest('POST', $path, $headers, json_encode($body), $this->client->isJSONResponse());
//     }
// }
