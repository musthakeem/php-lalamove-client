<?php

namespace JM\Lalamove;

/**
 * Handles operations related to drivers in Lalamove orders.
 */
class Driver
{
    /**
     * @var LalamoveClient The client used to interact with the Lalamove API.
     */
    private $client;

    /**
     * Valid cancellation reasons according to Lalamove API documentation
     */
    private const VALID_CANCEL_REASONS = [
        'DRIVER_UNRESPONSIVE',
        'INCORRECT_DRIVER_INFORMATION',
        'INCORRECT_VEHICLE_INFORMATION',
        'OTHER'
    ];

    /**
     * Constructs a new Driver instance.
     *
     * @param LalamoveClient $client The Lalamove client to handle API requests.
     */
    public function __construct(LalamoveClient $client)
    {
        $this->client = $client;
    }

    /**
     * Cancels a driver from an order with a specified reason.
     *
     * @param string $orderId The unique identifier for the order.
     * @param string $driverId The unique identifier for the driver.
     * @param string $reason The reason for canceling the driver (default: 'DRIVER_UNRESPONSIVE').
     * @return mixed The API response.
     * @throws \InvalidArgumentException If the order ID, driver ID is empty or reason is invalid.
     */
    public function cancel(string $orderId, string $driverId, string $reason = 'DRIVER_UNRESPONSIVE')
    {
        $this->validateOrderId($orderId);
        $this->validateDriverId($driverId);
        $this->validateCancelReason($reason);

        $path = "/v3/orders/{$orderId}/drivers/{$driverId}";
        $body = json_encode(['data' => ['reason' => $reason]]);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid cancel payload: ' . json_last_error_msg());
        }
        
        $headers = $this->getHeaders('DELETE', $path, $body);
        return $this->client->makeRequest('DELETE', $path, $headers, $body);
    }

    /**
     * Retrieves details of a driver assigned to a specific order.
     *
     * @param string $orderId The unique identifier for the order.
     * @param string $driverId The unique identifier for the driver.
     * @return mixed The API response.
     * @throws \InvalidArgumentException If the order ID or driver ID is empty.
     */
    public function retrieve(string $orderId, string $driverId)
    {
        $this->validateOrderId($orderId);
        $this->validateDriverId($driverId);
        
        $path = "/v3/orders/{$orderId}/drivers/{$driverId}";
        $headers = $this->getHeaders('GET', $path);

        return $this->client->makeRequest('GET', $path, $headers);
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
    
    /**
     * Validates that a driver ID is not empty.
     *
     * @param string $driverId The driver ID to validate.
     * @throws \InvalidArgumentException If the driver ID is empty.
     */
    private function validateDriverId(string $driverId): void
    {
        if (empty(trim($driverId))) {
            throw new \InvalidArgumentException('Driver ID cannot be empty');
        }
    }
    
    /**
     * Validates that the cancel reason is valid.
     *
     * @param string $reason The reason for cancellation.
     * @throws \InvalidArgumentException If the reason is not valid.
     */
    private function validateCancelReason(string $reason): void
    {
        if (!in_array($reason, self::VALID_CANCEL_REASONS)) {
            throw new \InvalidArgumentException(
                'Invalid cancel reason. Must be one of: ' . implode(', ', self::VALID_CANCEL_REASONS)
            );
        }
    }
}
