<?php

namespace JM\Lalamove;

use JM\Lalamove\Http\HttpClient;
use JM\Lalamove\Payload\Quotation\QuotationPayloadBuilder;
use JM\Lalamove\Payload\Order\OrderPayloadBuilder;
use JM\Lalamove\Payload\Order\PatchOrderPayloadBuilder;

/**
 * Main client class for interacting with the Lalamove API.
 * Provides functionality to perform various operations related to quotations, orders, drivers, and more.
 */
class LalamoveClient
{
    /**
     * @var SignatureGenerator For generating signatures for API requests
     */
    private $signatureGenerator;
    
    /**
     * @var HttpClient For making HTTP requests to the API
     */
    private $httpClient;
    
    /**
     * @var Config Configuration object containing all settings
     */
    private $config;
    
    /**
     * @var Quotation Service for quotation-related operations
     */
    private $quotation;
    
    /**
     * @var Order Service for order-related operations
     */
    private $order;
    
    /**
     * @var Driver Service for driver-related operations
     */
    private $driver;
    
    /**
     * @var Market Service for market-related operations
     */
    private $markets;
    
    /**
     * @var City Service for city-related operations
     */
    private $city;
    
    /**
     * @var Webhook Service for webhook-related operations
     */
    private $webhook;

    /**
     * Constructor for the LalamoveClient that accepts a Config object.
     *
     * @param Config $config Configuration object with all required parameters
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->signatureGenerator = new SignatureGenerator(
            $config->getApiSecret(),
            $config->getApiKey()
        );
        $this->httpClient = new HttpClient();
        
        // Initialize related service objects
        $this->quotation = new Quotation($this);
        $this->order = new Order($this);
        $this->driver = new Driver($this);
        $this->markets = new Market($this);
        $this->city = new City($this);
        $this->webhook = new Webhook($this);
    }

    /**
     * Alternative constructor method to create a LalamoveClient with direct parameters
     * instead of a Config object. This maintains backward compatibility.
     *
     * @param string $apiKey API key for authentication
     * @param string $apiSecret API secret for generating signatures
     * @param string $market Market/country code for API interactions
     * @param string $environment Environment ('production' or 'sandbox')
     * @param bool|null $isJSON Whether responses should be returned as arrays
     * @param string $requestId Optional request identifier
     * @return LalamoveClient
     */
    public static function create(
        $apiKey,
        $apiSecret,
        $market,
        string $environment = 'sandbox',
        ?bool $isJSON = true,
        $requestId = ''
    ): LalamoveClient {
        $config = new Config($apiKey, $apiSecret, $market, $environment, $isJSON, $requestId);
        return new self($config);
    }

    // Getter methods
    
    /**
     * Get the SignatureGenerator instance
     * @return SignatureGenerator
     */
    public function getSignatureGenerator(): SignatureGenerator 
    { 
        return $this->signatureGenerator; 
    }
    
    /**
     * Get the market code
     * @return string
     */
    public function getMarket(): string 
    { 
        return $this->config->getMarket(); 
    }
    
    /**
     * Get the request ID
     * @return string
     */
    public function getRequestId(): string 
    { 
        return $this->config->getRequestId(); 
    }
    
    /**
     * Check if responses should be JSON
     * @return bool
     */
    public function isJSONResponse(): bool 
    { 
        return $this->config->isJSON(); 
    }
    
    /**
     * Get the base URL for API requests
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->config->getBaseUrl();
    }
    
    /**
     * Get the Config instance
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Create a new QuotationPayloadBuilder instance
     * @return QuotationPayloadBuilder
     */
    public function quotationPayloadBuilder(): QuotationPayloadBuilder 
    { 
        return new QuotationPayloadBuilder(); 
    }
    
    /**
     * Create a new OrderPayloadBuilder instance
     * @return OrderPayloadBuilder
     */
    public function orderPayloadBuilder(): OrderPayloadBuilder 
    { 
        return new OrderPayloadBuilder(); 
    }
    
    /**
     * Create a new PatchOrderPayloadBuilder instance
     * @return PatchOrderPayloadBuilder
     */
    public function patchOrderPayloadBuilder(): PatchOrderPayloadBuilder 
    { 
        return new PatchOrderPayloadBuilder(); 
    }
    
    /**
     * Get the Quotation service instance
     * @return Quotation
     */
    public function getQuotation() 
    { 
        return $this->quotation; 
    }
    
    /**
     * Get the Order service instance
     * @return Order
     */
    public function getOrder() 
    { 
        return $this->order; 
    }
    
    /**
     * Get the Driver service instance
     * @return Driver
     */
    public function getDriver() 
    { 
        return $this->driver; 
    }
    
    /**
     * Get the Market service instance
     * @return Market
     */
    public function getMarkets() 
    { 
        return $this->markets; 
    }
    
    /**
     * Get the City service instance
     * @return City
     */
    public function getCity() 
    { 
        return $this->city; 
    }
    
    /**
     * Get the Webhook service instance
     * @return Webhook
     */
    public function getWebhook() 
    { 
        return $this->webhook; 
    }

    /**
     * Generate a new request ID
     * @return string The new request ID
     */
    public function generateNewRequestId(): string
    {
        return $this->config->generateNewRequestId();
    }

    /**
     * Delegates API request handling to the internal HttpClient.
     *
     * @param string $method HTTP method ('GET', 'POST', 'PATCH', 'DELETE', etc.)
     * @param string $path API endpoint path
     * @param array $headers HTTP headers for the request
     * @param string $body Request body for methods that require it
     * @return mixed API response, decoded from JSON if isJSON is true
     */
    public function makeRequest($method, $path, $headers = [], $body = '')
    {
        $url = $this->config->getBaseUrl() . $path;
        return $this->httpClient->makeRequest($method, $url, $headers, $body, $this->config->isJSON());
    }
}
