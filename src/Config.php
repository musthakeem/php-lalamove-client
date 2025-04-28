<?php

namespace JM\Lalamove;

/**
 * Configuration class for Lalamove API client.
 * This centralizes all configuration parameters for easier management and validation.
 */
class Config
{
    /**
     * @var string API key for authentication
     */
    private $apiKey;
    
    /**
     * @var string API secret for generating signatures
     */
    private $apiSecret;
    
    /**
     * @var string Market/country code for API interactions
     */
    private $market;
    
    /**
     * @var string Environment ('production' or 'sandbox')
     */
    private $environment;
    
    /**
     * @var bool Whether responses should be returned as arrays
     */
    private $isJSON;
    
    /**
     * @var string Request identifier
     */
    private $requestId;
    
    /**
     * @var string Base URL for API requests
     */
    private $baseUrl;
    
    /**
     * Constructor for Config.
     *
     * @param string $apiKey API key for authentication.
     * @param string $apiSecret API secret for generating signatures.
     * @param string $market Market/country code for the API interactions.
     * @param string $environment Specifies the environment ('production' or 'sandbox').
     * @param bool $isJSON Whether the responses should be returned as arrays.
     * @param string $requestId Optional request identifier, auto-generated if not provided.
     * 
     * @throws \InvalidArgumentException If required parameters are missing or invalid
     */
    public function __construct(
        string $apiKey,
        string $apiSecret,
        string $market,
        string $environment = 'sandbox',
        bool $isJSON = true,
        string $requestId = ''
    ) {
        // Validate required parameters
        if (empty($apiKey)) {
            throw new \InvalidArgumentException('API key cannot be empty');
        }
        
        if (empty($apiSecret)) {
            throw new \InvalidArgumentException('API secret cannot be empty');
        }
        
        if (empty($market)) {
            throw new \InvalidArgumentException('Market cannot be empty');
        }
        
        // Validate environment
        if (!in_array($environment, ['production', 'sandbox'])) {
            throw new \InvalidArgumentException('Environment must be either "production" or "sandbox"');
        }
        
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->market = $market;
        $this->environment = $environment;
        $this->isJSON = $isJSON;
        $this->requestId = $requestId ?: uniqid();
        
        // Set the base URL based on environment
        $this->baseUrl = ($environment === 'production')
            ? 'https://rest.lalamove.com'
            : 'https://rest.sandbox.lalamove.com';
    }
    
    /**
     * Get the API key.
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
    
    /**
     * Get the API secret.
     *
     * @return string
     */
    public function getApiSecret(): string
    {
        return $this->apiSecret;
    }
    
    /**
     * Get the market code.
     *
     * @return string
     */
    public function getMarket(): string
    {
        return $this->market;
    }
    
    /**
     * Get the environment.
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }
    
    /**
     * Check if responses should be JSON.
     *
     * @return bool
     */
    public function isJSON(): bool
    {
        return $this->isJSON;
    }
    
    /**
     * Get the request ID.
     *
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }
    
    /**
     * Get the base URL for API requests.
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
    
    /**
     * Set a new request ID.
     *
     * @param string $requestId New request identifier
     * @return void
     */
    public function setRequestId(string $requestId): void
    {
        $this->requestId = $requestId;
    }
    
    /**
     * Generate a new unique request ID and set it.
     *
     * @return string The newly generated request ID
     */
    public function generateNewRequestId(): string
    {
        $this->requestId = uniqid();
        return $this->requestId;
    }
}