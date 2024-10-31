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
    private $signatureGenerator;
    private $market;
    private $requestId;
    private $baseUrl;
    private $httpClient;
    private $isJSON;
    private $quotation;
    private $order;
    private $driver;
    private $markets;
    private $city;
    private $webhook;

    /**
     * Constructor for the LalamoveClient.
     *
     * @param string $apiKey API key for authentication.
     * @param string $apiSecret API secret for generating signatures.
     * @param string $market Market/country code for the API interactions.
     * @param string $environment Specifies the environment ('production' or 'sandbox').
     * @param bool|null $isJSON Whether the responses should be returned as arrays.
     * @param string $requestId Optional request identifier, auto-generated if not provided.
     */
    public function __construct($apiKey, $apiSecret, $market, string $environment = 'sandbox', ?bool $isJSON = true, $requestId = '')
    {
        $this->signatureGenerator = new SignatureGenerator($apiSecret, $apiKey);
        $this->market = $market;
        $this->requestId = $requestId ?: uniqid();
        $this->isJSON = $isJSON;
        $this->baseUrl = ($environment === 'production') ? 'https://rest.lalamove.com' : 'https://rest.sandbox.lalamove.com';
        $this->httpClient = new HttpClient();
        
        // Initialize related service objects
        $this->quotation = new Quotation($this);
        $this->order = new Order($this);
        $this->driver = new Driver($this);
        $this->markets = new Market($this);
        $this->city = new City($this);
        $this->webhook = new Webhook($this);
    }

    // Getter methods for internal properties
    public function getSignatureGenerator() { return $this->signatureGenerator; }
    public function getMarket(): string { return $this->market; }
    public function getRequestId(): string { return $this->requestId; }
    public function isJSONResponse(): bool { return $this->isJSON; }
    public function quotationPayloadBuilder(): QuotationPayloadBuilder { return new QuotationPayloadBuilder(); }
    public function orderPayloadBuilder(): OrderPayloadBuilder { return new OrderPayloadBuilder(); }
    public function patchOrderPayloadBuilder(): PatchOrderPayloadBuilder { return new PatchOrderPayloadBuilder(); }
    public function getQuotation() { return $this->quotation; }
    public function getOrder() { return $this->order; }
    public function getDriver() { return $this->driver; }
    public function getMarkets() { return $this->markets; }
    public function getCity() { return $this->city; }
    public function getWebhook() { return $this->webhook; }

    /**
     * Delegates API request handling to the internal HttpClient.
     *
     * @param string $method HTTP method ('GET', 'POST', 'PATCH', 'DELETE', etc.).
     * @param string $path API endpoint path.
     * @param array $headers HTTP headers for the request.
     * @param string $body Request body for methods that require it.
     * @return mixed API response, decoded from JSON if isJSON is true.
     */
    public function makeRequest($method, $path, $headers = [], $body = '')
    {
        $url = $this->baseUrl . $path;
        return $this->httpClient->makeRequest($method, $url, $headers, $body, $this->isJSON);
    }
}


// namespace JMusthakeem\Lalamove;

// use JMusthakeem\Lalamove\Http\HttpClient;
// use JMusthakeem\Lalamove\Payload\Quotation\QuotationPayloadBuilder;
// use JMusthakeem\Lalamove\Payload\Order\OrderPayloadBuilder;
// use JMusthakeem\Lalamove\Payload\Order\PatchOrderPayloadBuilder;

// class LalamoveClient
// {
//     private $signatureGenerator;
//     private $market;
//     private $requestId;
//     private $baseUrl;
//     private $httpClient;
//     private $isJSON;
//     private $quotation;
//     private $order;
//     private $driver;
//     private $markets;
//     private $city;
//     private $webhook;

//     public function __construct($apiKey, $apiSecret, $market, string $environment = 'sandbox', ?bool $isJSON = true, $requestId = '' )
//     {
//         $this->signatureGenerator = new SignatureGenerator($apiSecret, $apiKey);
//         $this->market = $market;
//         $this->requestId = $requestId ?: uniqid();
//         $this->isJSON = $isJSON;
//         $this->baseUrl = ($environment === 'production')
//             ? 'https://rest.lalamove.com'
//             : 'https://rest.sandbox.lalamove.com';
            
//         $this->httpClient   = new HttpClient(); // Instantiate HttpClient
//         $this->quotation    = new Quotation($this);
//         $this->order        = new Order($this);
//         $this->driver       = new Driver($this);
//         $this->markets      = new Market($this);
//         $this->city         = new City($this);
//         $this->webhook      = new Webhook($this);
//     }

//     public function getSignatureGenerator()
//     {
//         return $this->signatureGenerator;
//     }

//     public function getMarket(): string
//     {
//         return $this->market;
//     }

//     public function getRequestId(): string
//     {
//         return $this->requestId;
//     }

//     public function isJSONResponse(): bool
//     {
//         return $this->isJSON;
//     }

//     public function quotationPayloadBuilder(): QuotationPayloadBuilder
//     {
//         return new QuotationPayloadBuilder();
//     }

//     public function orderPayloadBuilder(): OrderPayloadBuilder
//     {
//         return new OrderPayloadBuilder();
//     }

//     public function patchOrderPayloadBuilder(): PatchOrderPayloadBuilder
//     {
//         return new PatchOrderPayloadBuilder();
//     }

//     public function getQuotation(){
//         return $this->quotation;
//     }

//     public function getOrder(){
//         return $this->order;
//     }

//     public function getDriver(){
//         return $this->driver;
//     }

//     public function getMarkets(){
//         return $this->markets;
//     }

//     public function getCity(){
//         return $this->city;
//     }

//     public function getWebhook(){
//         return $this->webhook;
//     }

//     public function makeRequest($method, $path, $headers = [], $body = '')
//     {
//         $url = $this->baseUrl . $path;
//         return $this->httpClient->makeRequest($method, $url, $headers, $body, $this->isJSON); // Delegate to HttpClient
//     }
// }
