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
     * @throws \InvalidArgumentException If the payload is invalid
     */
    public function create(array $payload)
    {
        // Validate the payload
        if (empty($payload)) {
            throw new \InvalidArgumentException('Quotation payload cannot be empty');
        }

        $path = '/v3/quotations';
        $body = json_encode(["data" => $payload]);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid quotation payload: ' . json_last_error_msg());
        }
        
        $headers = $this->getHeaders('POST', $path, $body);
        return $this->client->makeRequest('POST', $path, $headers, $body);
    }

    /**
     * Retrieves an existing quotation from the Lalamove API.
     *
     * @param string $quotationId The unique identifier for the quotation to retrieve.
     * @return mixed The API response.
     * @throws \InvalidArgumentException If the quotation ID is empty
     */
    public function retrieve(string $quotationId)
    {
        $this->validateQuotationId($quotationId);
        
        $path = "/v3/quotations/{$quotationId}";
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
     * Validates that a quotation ID is not empty.
     *
     * @param string $quotationId The quotation ID to validate.
     * @throws \InvalidArgumentException If the quotation ID is empty.
     */
    private function validateQuotationId(string $quotationId): void
    {
        if (empty(trim($quotationId))) {
            throw new \InvalidArgumentException('Quotation ID cannot be empty');
        }
    }
}
