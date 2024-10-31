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
     */
    public function cancel(string $orderId, string $driverId, string $reason = 'DRIVER_UNRESPONSIVE')
    {
        $path = "/v3/orders/{$orderId}/drivers/{$driverId}";
        $body = json_encode(['data' => ['reason' => $reason]]);
        $headers = $this->client->getSignatureGenerator()->getHeaders('DELETE', $path, $this->client->getMarket(), $body, $this->client->getRequestId());

        return $this->client->makeRequest('DELETE', $path, $headers, $body);
    }

    /**
     * Retrieves details of a driver assigned to a specific order.
     *
     * @param string $orderId The unique identifier for the order.
     * @param string $driverId The unique identifier for the driver.
     * @return mixed The API response.
     */
    public function retrieve(string $orderId, string $driverId)
    {
        $path = "/v3/orders/{$orderId}/drivers/{$driverId}";
        $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path, $this->client->getMarket(), '', $this->client->getRequestId());

        return $this->client->makeRequest('GET', $path, $headers, '');
    }
}




// namespace JMusthakeem\Lalamove;

// class Driver
// {
//     private $client;

//     public function __construct(LalamoveClient $client)
//     {
//         $this->client = $client;
//     }

//     public function cancel($orderId, $driverId, $reason = 'DRIVER_UNRESPONSIVE')
//     {
//         $path = "/v3/orders/{$orderId}/drivers/{$driverId}";
//         $body = ['data'=>['reason'=>"{$reason}"]];
//         $headers = $this->client->getSignatureGenerator()->getHeaders('DELETE', $path, $this->client->getMarket(), json_encode($body), $this->client->getRequestId());

//         return $this->client->makeRequest('DELETE', $path, $headers, json_encode($body), $this->client->isJSONResponse());
//     }

//     public function retrieve($orderId, $driverId)
//     {
//         $path = "/v3/orders/{$orderId}/drivers/{$driverId}";
//         $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path,$this->client->getMarket(), '', $this->client->getRequestId());

//         return $this->client->makeRequest('GET', $path, $headers, '', $this->client->isJSONResponse());

//     }
// }
