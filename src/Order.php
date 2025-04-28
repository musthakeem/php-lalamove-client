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
     * @throws \InvalidArgumentException If the payload is invalid.
     */
    public function create(array $payload)
    {
        // Validate the payload
        if (empty($payload)) {
            throw new \InvalidArgumentException('Order payload cannot be empty');
        }

        $path = '/v3/orders';
        $body = json_encode(["data" => $payload]);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid order payload: ' . json_last_error_msg());
        }
        
        $headers = $this->getHeaders('POST', $path, $body);
        return $this->client->makeRequest('POST', $path, $headers, $body);
    }

    /**
     * Retrieves an existing order by its ID.
     *
     * @param string $orderId The unique identifier for the order.
     * @return mixed The API response.
     * @throws \InvalidArgumentException If the order ID is empty.
     */
    public function retrieve(string $orderId)
    {
        $this->validateOrderId($orderId);
        $path = "/v3/orders/{$orderId}";
        $headers = $this->getHeaders('GET', $path);

        return $this->client->makeRequest('GET', $path, $headers);
    }

    /**
     * Cancels an order by its ID.
     *
     * @param string $orderId The unique identifier for the order to be cancelled.
     * @return mixed The API response.
     * @throws \InvalidArgumentException If the order ID is empty.
     */
    public function cancel(string $orderId)
    {
        $this->validateOrderId($orderId);
        $path = "/v3/orders/{$orderId}";
        $headers = $this->getHeaders('DELETE', $path);

        return $this->client->makeRequest('DELETE', $path, $headers);
    }

    /**
     * Edits an existing order by updating its payload.
     *
     * @param string $orderId The unique identifier for the order to be updated.
     * @param array $payload The data payload with the new order details.
     * @return mixed The API response.
     * @throws \InvalidArgumentException If the order ID or payload is invalid.
     */
    public function edit(string $orderId, array $payload)
    {
        $this->validateOrderId($orderId);
        
        // Validate the payload
        if (empty($payload)) {
            throw new \InvalidArgumentException('Edit payload cannot be empty');
        }

        $path = "/v3/orders/{$orderId}";
        $body = json_encode(["data" => ['stops' => $payload]]);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid edit payload: ' . json_last_error_msg());
        }
        
        $headers = $this->getHeaders('PATCH', $path, $body);
        return $this->client->makeRequest('PATCH', $path, $headers, $body);
    }
    
    /**
     * Adds a priority fee to an existing order.
     *
     * @param string $orderId The unique identifier for the order.
     * @param string|float $fee The amount of the priority fee to be added.
     * @return mixed The API response.
     * @throws \InvalidArgumentException If the order ID or fee is invalid.
     */
    public function addPriorityFee(string $orderId, $fee)
    {
        $this->validateOrderId($orderId);
        
        // Validate the fee
        if (!is_numeric($fee) || (float)$fee <= 0) {
            throw new \InvalidArgumentException('Priority fee must be a positive number');
        }
        
        $path = "/v3/orders/{$orderId}/priority-fee";
        $body = json_encode(["data" => ["priorityFee" => (string)$fee]]);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid priority fee format: ' . json_last_error_msg());
        }
        
        $headers = $this->getHeaders('POST', $path, $body);
        return $this->client->makeRequest('POST', $path, $headers, $body);
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
    
    /**
     * Validates that an order ID is not empty.
     *
     * @param string $orderId The order ID to validate.
     * @throws \InvalidArgumentException If the order ID is empty.
     */
    private function validateOrderId(string $orderId): void
    {
        if (empty(trim($orderId))) {
            throw new \InvalidArgumentException('Order ID cannot be empty');
        }
    }
}
