<?php

namespace JM\Lalamove;

/**
 * Handles the creation of HMAC signatures and related headers for API requests.
 */
class SignatureGenerator
{
    /**
     * The secret key used for generating the HMAC signature.
     * @var string
     */
    private $secret;

    /**
     * The API key included in the authorization token.
     * @var string
     */
    private $apiKey;

    /**
     * Constructor for the SignatureGenerator.
     *
     * @param string $secret The secret key for HMAC signature.
     * @param string $apiKey The public API key.
     */
    public function __construct(string $secret, string $apiKey)
    {
        $this->secret = $secret;
        $this->apiKey = $apiKey;
    }

    /**
     * Generates an HMAC signature based on the provided request details.
     *
     * @param string $method The HTTP method (e.g., 'GET', 'POST').
     * @param string $path The API path.
     * @param string $body The request body as a JSON string.
     * @param int|null $time The timestamp for the signature, default is current time in milliseconds.
     * @return array Returns an array containing the signature and the timestamp.
     */
    public function generateSignature(string $method, string $path, string $body = '', ?int $time = null): array
    {
        $time = $time ?? (time() * 1000);  // Use current time in milliseconds if not provided.
        $rawSignature = $this->createRawSignature($time, $method, $path, $body);
        $signature = $this->hmacSha256($rawSignature, $this->secret);
        return [$signature, $time];
    }

    /**
     * Creates a token using the API key, time, and the signature.
     *
     * @param string $signature The HMAC signature.
     * @param int $time The time the signature was generated.
     * @return string Returns the complete authorization token.
     */
    public function createToken(string $signature, int $time): string
    {
        return "{$this->apiKey}:{$time}:{$signature}";
    }

    /**
     * Prepares the necessary headers for an API request including the authorization token.
     *
     * @param string $method The HTTP method used for the API request.
     * @param string $path The API path.
     * @param string $market The market/country code for the request.
     * @param string $body The request body as a JSON string.
     * @param string|null $requestId An optional unique identifier for the request.
     * @return array Returns an array of headers for the HTTP request.
     */
    public function getHeaders(string $method, string $path, string $market, string $body = '', ?string $requestId = null): array
    {
        list($signature, $time) = $this->generateSignature($method, $path, $body);
        $token = $this->createToken($signature, $time);

        $headers = [
            "Authorization: hmac {$token}",
            "Market: {$market}",
            "Content-Type: application/json"
        ];

        if ($requestId !== null) {
            $headers[] = "Request-ID: {$requestId}";
        }

        return $headers;
    }

    /**
     * Assembles the raw data string that will be signed.
     *
     * @param int $time The time the signature is generated.
     * @param string $method The HTTP method.
     * @param string $path The API path.
     * @param string $body The request body, included only for non-GET requests.
     * @return string Returns the constructed data string to be hashed.
     */
    private function createRawSignature(int $time, string $method, string $path, string $body): string
    {
        $method = strtoupper($method);
        $bodyComponent = $method === 'GET' ? "\r\n\r\n" : "\r\n\r\n{$body}";
        return "{$time}\r\n{$method}\r\n{$path}{$bodyComponent}";
    }

    /**
     * Computes the HMAC SHA256 hash of the provided data using the secret key.
     *
     * @param string $data The string to be hashed.
     * @param string $secret The secret key.
     * @return string Returns the HMAC hash.
     */
    private function hmacSha256(string $data, string $secret): string
    {
        return hash_hmac('sha256', $data, $secret);
    }
}





// namespace JMusthakeem\Lalamove;

// class SignatureGenerator
// {
//     private $secret;
//     private $apiKey;

//     public function __construct($secret, $apiKey)
//     {
//         $this->secret = $secret;
//         $this->apiKey = $apiKey;
//     }

//     public function generateSignature($method, $path, $body = '', $time = null)
//     {
//         if ($time === null) {
//             $time = time() * 1000; // Default to current time if not provided
//         }

//         $rawSignature = $this->createRawSignature($time, $method, $path, $body);
//         $signature = $this->hmacSha256($rawSignature, $this->secret);
//         return [$signature, $time];
//     }

//     public function createToken($signature, $time)
//     {
//         return "{$this->apiKey}:{$time}:{$signature}";
//     }

//     public function getHeaders($method, $path, $market, $body = '', $requestId = null)
//     {
//         list($signature, $time) = $this->generateSignature($method, $path, $body);
//         $token = $this->createToken($signature, $time);

//         $headers = [
//             'Authorization: hmac ' . $token,
//             'Market: ' . $market,
//             'Content-Type: application/json'
//         ];

//         if ($requestId !== null) {
//             $headers[] = 'Request-ID: ' . $requestId;
//         }

//         return $headers;
//     }

//     private function createRawSignature($time, $method, $path, $body)
//     {
//         if (strtoupper($method) === 'GET') {
//             return "{$time}\r\n{$method}\r\n{$path}\r\n\r\n";
//         } else {
//             return "{$time}\r\n{$method}\r\n{$path}\r\n\r\n{$body}";
//         }
//     }

//     private function hmacSha256($data, $secret)
//     {
//         return hash_hmac('sha256', $data, $secret);
//     }
// }

