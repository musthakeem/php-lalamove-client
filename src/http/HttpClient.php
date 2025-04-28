<?php

namespace JM\Lalamove\Http;

/**
 * HttpClient is responsible for making HTTP requests using cURL.
 * It supports various HTTP methods like GET, POST, PUT, PATCH, and DELETE.
 * This optimized version includes better error handling, performance tuning, and response processing.
 */
class HttpClient
{
    /**
     * @var resource cURL handle used to perform HTTP requests.
     */
    private $curlHandle;

    /**
     * @var array Default cURL options applied to all requests
     */
    private $defaultOptions = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_VERIFYPEER => true,
    ];

    /**
     * Initializes a new HttpClient instance and sets up the cURL handle.
     */
    public function __construct()
    {
        $this->initCurlHandle();
    }

    /**
     * Initializes the cURL handle with default options if it's not already initialized.
     * @return void
     */
    private function initCurlHandle(): void
    {
        if (!$this->curlHandle) {
            $this->curlHandle = curl_init();
            
            // Apply default options
            foreach ($this->defaultOptions as $option => $value) {
                curl_setopt($this->curlHandle, $option, $value);
            }
        }
    }

    /**
     * Makes an HTTP request to the specified URL with the provided method, headers, and body.
     *
     * @param string $method The HTTP method to use (GET, POST, PUT, PATCH, DELETE).
     * @param string $url The URL to which the request is made.
     * @param array $headers Optional headers to include in the request.
     * @param string $body Optional body content for POST, PUT, PATCH, or DELETE requests.
     * @param bool $isJSONResponse Whether the response should be returned as an array (default: true).
     * @return mixed The API response as an associative array or JSON string, or an error message.
     */
    public function makeRequest(string $method, string $url, array $headers = [], string $body = '', bool $isJSONResponse = true)
    {
        // Reset cURL handle to ensure clean request (avoids cross-request issues)
        $this->resetCurlHandle();
        
        // Set cURL options for the request
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);
        curl_setopt($this->curlHandle, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        // Set request body if applicable
        if (in_array(strtoupper($method), ['POST', 'PUT', 'PATCH', 'DELETE']) && !empty($body)) {
            curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $body);
        }

        // Set headers if provided
        if (!empty($headers)) {
            curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $headers);
        }

        // Execute the request and capture the response
        $response = curl_exec($this->curlHandle);
        $curlError = curl_errno($this->curlHandle);
        $httpCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
        
        // Handle cURL errors
        if ($curlError !== 0) {
            $errorMessage = curl_error($this->curlHandle);
            error_log('Curl error ' . $curlError . ': ' . $errorMessage);
            
            $errorResponse = [
                'error' => 'Communication Error',
                'code' => $curlError,
                'message' => $errorMessage,
            ];
            
            return $isJSONResponse ? $errorResponse : json_encode($errorResponse);
        }

        // Handle successful responses (HTTP status code 2xx)
        if ($httpCode >= 200 && $httpCode < 300) {
            // Special case for successful DELETE operations without content
            if ($httpCode == 204 && strtoupper($method) == 'DELETE') {
                $result = ['success' => true, 'statusCode' => $httpCode];
                return $isJSONResponse ? $result : json_encode($result, JSON_PRETTY_PRINT);
            }

            // Process regular responses with content
            if (empty($response)) {
                $result = ['success' => true, 'statusCode' => $httpCode];
            } else {
                // Try to decode as JSON and handle failures
                $decodedResponse = $this->safeJsonDecode($response);
                $result = $decodedResponse !== null ? $decodedResponse : $response;
            }
            
            return $isJSONResponse ? $result : json_encode($result, JSON_PRETTY_PRINT);
        } else {
            // Handle non-successful HTTP responses
            $error = $this->processErrorResponse($response, $httpCode);
            return $isJSONResponse ? $error : json_encode($error, JSON_PRETTY_PRINT);
        }
    }

    /**
     * Safely decode a JSON response and handle potential errors.
     *
     * @param string $response The JSON string to decode
     * @return mixed The decoded result or null on failure
     */
    private function safeJsonDecode(string $response)
    {
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('JSON decode error: ' . json_last_error_msg());
            return null;
        }
        
        return $result;
    }

    /**
     * Process error responses and convert them into meaningful error objects.
     *
     * @param string $response The response body
     * @param int $httpCode The HTTP status code
     * @return array An error result object
     */
    private function processErrorResponse(string $response, int $httpCode): array
    {
        $error = [
            'error' => 'HTTP Error',
            'statusCode' => $httpCode,
        ];
        
        // Try to parse response body for more details
        $decodedError = $this->safeJsonDecode($response);
        
        if ($decodedError !== null) {
            $error['details'] = $decodedError;
        } else {
            $error['rawResponse'] = $response;
        }
        
        // Log error for debugging
        error_log('HTTP error ' . $httpCode . ': ' . $response);
        
        return $error;
    }

    /**
     * Resets the cURL handle to ensure clean state between requests
     * @return void
     */
    private function resetCurlHandle(): void
    {
        if ($this->curlHandle) {
            curl_reset($this->curlHandle);
            
            // Reapply default options
            foreach ($this->defaultOptions as $option => $value) {
                curl_setopt($this->curlHandle, $option, $value);
            }
        } else {
            $this->initCurlHandle();
        }
    }

    /**
     * Destructor to close the cURL handle when the instance is destroyed.
     */
    public function __destruct()
    {
        if ($this->curlHandle) {
            curl_close($this->curlHandle);
            $this->curlHandle = null;
        }
    }
}
  git config --global user.email "jmusthak.devlp@gmail.com"
  git config --global user.name "Jithu Musthakeem"