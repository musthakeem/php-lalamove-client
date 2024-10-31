<?php

namespace JM\Lalamove\Http;

/**
 * HttpClient is responsible for making HTTP requests using cURL.
 * It supports various HTTP methods like GET, POST, PUT, PATCH, and DELETE.
 */
class HttpClient
{
    /**
     * @var resource cURL handle used to perform HTTP requests.
     */
    private $curlHandle;

    /**
     * Initializes a new HttpClient instance and sets up the cURL handle.
     */
    public function __construct()
    {
        $this->initCurlHandle();
    }

    /**
     * Initializes the cURL handle if it's not already initialized.
     */
    private function initCurlHandle()
    {
        if (!$this->curlHandle) {
            $this->curlHandle = curl_init();
        }
    }

    /**
     * Makes an HTTP request to the specified URL with the provided method, headers, and body.
     *
     * @param string $method The HTTP method to use (GET, POST, PUT, PATCH, DELETE).
     * @param string $url The URL to which the request is made.
     * @param array $headers Optional headers to include in the request.
     * @param string $body Optional body content for POST, PUT, PATCH, or DELETE requests.
     * @param bool|null $isJSONResponse Whether the response should be returned as an array (default: true).
     * @return mixed The API response as an associative array or JSON string, or an error message.
     */
    public function makeRequest($method, $url, $headers = [], $body = '', ?bool $isJSONResponse = true)
    {
        // Set cURL options for the request
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        // Set request body if applicable
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE']) && !empty($body)) {
            curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $body);
        }

        // Set headers if provided
        if (!empty($headers)) {
            curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $headers);
        }

        // Execute the request and capture the response
        $response = curl_exec($this->curlHandle);

        // Handle cURL errors
        if (curl_errno($this->curlHandle)) {
            error_log('Curl error: ' . curl_error($this->curlHandle));
            return ['error' => 'An internal error occurred'];
        }

        // Get the HTTP response code
        $httpCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);

        // Handle successful responses (HTTP status code 2xx)
        if ($httpCode >= 200 && $httpCode < 300) {

            if ($httpCode == 204 && $method == 'DELETE') {
                return true;  // No content response for DELETE
            }

            // Decode the response if JSON
            $result = json_decode($response);

            // Return the appropriate format based on $isJSONResponse
            if ($isJSONResponse) {
                return json_encode($result, JSON_PRETTY_PRINT);
            }

            return $result;

        } else {
            // Log and handle non-2xx HTTP responses
            // error_log('HTTP error: ' . $httpCode . ' Response: ' . $response);

            // Decode the response if JSON
            $error = json_decode($response);

            // Return the appropriate format based on $isJSONResponse
            if ($isJSONResponse) {
                return json_encode($error, JSON_PRETTY_PRINT);
            }

            return $error;
            // return ['error' => 'HTTP error', 'status_code' => $httpCode];
        }
    }

    /**
     * Destructor to close the cURL handle when the instance is destroyed.
     */
    public function __destruct()
    {
        if ($this->curlHandle) {
            curl_close($this->curlHandle);
        }
    }
}


// namespace JMusthakeem\Lalamove\Http;

// class HttpClient
// {
//     private $curlHandle;

//     public function __construct()
//     {
//         $this->initCurlHandle();
//     }

//     private function initCurlHandle()
//     {
//         if (!$this->curlHandle) {
//             $this->curlHandle = curl_init();
//         }
//     }

//     public function makeRequest($method, $url, $headers = [], $body = '', ?bool $isJSONResponse = true)
//     {
//         curl_setopt($this->curlHandle, CURLOPT_URL, $url);
//         curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($this->curlHandle, CURLOPT_CUSTOMREQUEST, $method);

//         if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE']) && !empty($body)) {
//             curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $body);
       
//         } // Set headers
//         if (!empty($headers)) {
//             curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $headers);
//         }

//         $response = curl_exec($this->curlHandle);

//         if (curl_errno($this->curlHandle)) {
//             error_log('Curl error: ' . curl_error($this->curlHandle));
//             return ['error' => 'An internal error occurred'];
//         }

//         $httpCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);


//         if ($httpCode >= 200 && $httpCode < 300) {
            
//             if ($httpCode == 204 && $method == 'DELETE') {
//                 return true;
//             }

//             $result = json_decode($response, true);
            
//             if (!$isJSONResponse) {
//                 return json_encode($result, JSON_PRETTY_PRINT);
//             }
            
//             return $result;

//         } else {
//             error_log('HTTP error: ' . $httpCode . ' Response: ' . $response);
//             return ['error' => 'HTTP error', 'status_code' => $httpCode];
//         }
//     }

//     public function __destruct()
//     {
//         if ($this->curlHandle) {
//             curl_close($this->curlHandle);
//         }
//     }
// }
