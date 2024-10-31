<?php

namespace JM\Lalamove;

use Exception;

/**
 * Handles retrieval and processing of city data through the Lalamove API.
 */
class City
{
    /**
     * @var LalamoveClient The client instance used to interact with the API.
     */
    private $client;

    /**
     * Constructs a new City instance.
     *
     * @param LalamoveClient $client The client used for making API requests.
     */
    public function __construct(LalamoveClient $client)
    {
        $this->client = $client;
    }

    /**
     * Transforms the cities by converting 'locode' to 'id'.
     *
     * @param array $cities The array of cities to transform.
     * @return array The transformed array of cities.
     */
    private function transformCities(array $cities): array
    {
        foreach ($cities as &$city) {
            if (isset($city['locode'])) {
                $city['id'] = $city['locode'];
                unset($city['locode']);
            }
        }
        return $cities;
    }

    /**
     * Finds a city by its ID.
     *
     * @param array $cities The array of cities to search.
     * @param string $cityId The ID of the city to find.
     * @return array|null The city data if found, or null if not found.
     */
    private function findCityById(array $cities, string $cityId): ?array
    {
        foreach ($cities as $city) {
            if ($city['id'] === $cityId) {
                return $city;
            }
        }
        return null;
    }

    /**
     * Fetches city data from the API.
     *
     * @param string $path The API path.
     * @param array $headers The headers to include in the request.
     * @return mixed The API response.
     */
    private function fetchCitiesData(string $path, array $headers)
    {
        $result = $this->client->makeRequest('GET', $path, $headers, '');
        
        return $this->client->isJSONResponse() ? json_decode($result, true) : json_decode(json_encode($result), true);
    }

    /**
     * Validates if the API response contains a valid data structure.
     *
     * @param mixed $response The API response to validate.
     * @return bool True if valid, false otherwise.
     */
    private function isValidResponse($response): bool
    {
        return isset($response['data']) && is_array($response['data']);
    }

    /**
     * Finds the closest service key based on the load value.
     *
     * @param array $citiesData The array of cities containing service information.
     * @param float $targetLoad The target load value to find the closest service.
     * @return string|null The closest service key or null if not found.
     */
    private function findClosestServiceByLoad(array $citiesData, float $targetLoad): ?string
    {
        $closestService = null;
        $closestLoad = PHP_FLOAT_MAX;

        foreach ($citiesData as $city) {
            foreach ($city['services'] as $service) {
                if (isset($service['load']['value'])) {
                    $serviceLoad = floatval($service['load']['value']);
                    if ($serviceLoad >= $targetLoad && $serviceLoad < $closestLoad) {
                        $closestLoad = $serviceLoad;
                        $closestService = $service['key'];
                    }
                }
            }
        }

        return $closestService;
    }

    /**
     * Finds the closest service key for a specific city based on load value.
     *
     * @param array $city The city data.
     * @param float $targetLoad The target load value to find the closest service.
     * @return string|null The closest service key or null if not found.
     */
    private function findCityServiceByLoad(array $city, float $targetLoad): ?string
    {
        $closestService = null;
        $closestLoad = PHP_FLOAT_MAX;

        foreach ($city['services'] as $service) {
            if (isset($service['load']['value'])) {
                $serviceLoad = floatval($service['load']['value']);
                if ($serviceLoad >= $targetLoad && $serviceLoad < $closestLoad) {
                    $closestLoad = $serviceLoad;
                    $closestService = $service['key'];
                }
            }
        }

        return $closestService;
    }

    /**
     * Retrieves the city data by its ID.
     *
     * @param string $cityId The city ID.
     * @param string $reqMarket Optional market to override the default market.
     * @return array The response containing city data or an error message.
     */
    public function retrieve(string $cityId, string $reqMarket = ''): string | object
    {
        $path = "/v3/cities";
        $market = $reqMarket ?: $this->client->getMarket();
        $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path, $market, '', $this->client->getRequestId());

        try {
            $response = $this->fetchCitiesData($path, $headers);
            
            if (!$this->isValidResponse($response)) {
                $error = ['error' => 'Invalid API response structure', 'status_code' => 500];                
                 return $this->client->isJSONResponse() ? json_encode($error , JSON_PRETTY_PRINT) : (object) $error;
            }

            $cities = $this->transformCities($response['data']);
            $foundCity = $this->findCityById($cities, $cityId);

            $city =  $foundCity ? ['data' => $foundCity] : ['error' => 'No such city with ID: ' . $cityId, 'status_code' => 404];
            return $this->client->isJSONResponse() ? json_encode($city , JSON_PRETTY_PRINT) : (object) $city;

        } catch (Exception $e) {
            $e = ['error' => $e->getMessage(), 'status_code' => 500];
            return $this->client->isJSONResponse() ? json_encode($e , JSON_PRETTY_PRINT) : (object) $e;
        }
    }

    /**
     * Retrieves the service key for the closest matching load.
     *
     * @param float $targetLoad The target load value to match.
     * @param string $cityId Optional city ID to search within a specific city.
     * @param string $reqMarket Optional market to override the default market.
     * @return array|null The service key if found, or an error message.
     */
    public function getServiceKeyByLoad(float $targetLoad, string $cityId, string $reqMarket = ''): string | object
    {
        $path = "/v3/cities";
        $market = $reqMarket ?: $this->client->getMarket();
        $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path, $market, '', $this->client->getRequestId());

        try {
            $response = $this->fetchCitiesData($path, $headers);
            
            if (!$this->isValidResponse($response)) {
                $error =  ['error' => 'Invalid API response structure', 'status_code' => 500];
                return $this->client->isJSONResponse() ? json_encode($error , JSON_PRETTY_PRINT) : (object) $error;

            }

            if ($cityId) {
                $cities = $this->transformCities($response['data']);
                $foundCity = $this->findCityById($cities, $cityId);
                
                $service = $foundCity
                   ? ['data' => ['serviceType' => $this->findCityServiceByLoad($foundCity, $targetLoad)]]
                   : ['error' => 'No such city with ID: ' . $cityId, 'status_code' => 404];
                
                   return $this->client->isJSONResponse() ? json_encode($service , JSON_PRETTY_PRINT) : (object) $service;
                }

            $service = ['data' => ['serviceType' => $this->findClosestServiceByLoad($response['data'], $targetLoad)]];
            return $this->client->isJSONResponse() ? json_encode($service , JSON_PRETTY_PRINT) : (object) $service;


        } catch (Exception $e) {
            $e = ['error' => $e->getMessage(), 'status_code' => 500];
            return $this->client->isJSONResponse() ? json_encode($e , JSON_PRETTY_PRINT) : (object) $e;
        }
    }
}


//V2.0
// namespace JMusthakeem\Lalamove;

// class City
// {
//     private $client;

//     public function __construct(LalamoveClient $client)
//     {
//         $this->client = $client;
//     }

//     /**
//      * Transforms the cities by converting 'locode' to 'id'.
//      */
//     private function transformCities(array $cities): array
//     {
//         foreach ($cities as &$city) {
//             if (isset($city['locode'])) {
//                 $city['id'] = $city['locode'];
//                 unset($city['locode']);
//             }
//         }
//         return $cities;
//     }

//     /**
//      * Finds the city by its ID.
//      */
//     private function findCityById(array $cities, $cityId): ?array
//     {
//         foreach ($cities as $city) {
//             if ($city['id'] === $cityId) {
//                 return $city;
//             }
//         }
//         return null;
//     }

//     /**
//      * Fetches city data from the API.
//      */
//     private function fetchCitiesData(string $path, array $headers)
//     {
//         return $this->client->makeRequest('GET', $path, $headers, '', $this->client->isJSONResponse());

//     }

//     /**
//      * Validates if the API response contains a valid data structure.
//      */
//     private function isValidResponse($response): bool
//     {
//         return isset($response['data']) && is_array($response['data']);
//     }

//     /**
//      * Finds the closest service key based on the load value.
//      */
//     private function findClosestServiceByLoad(array $citiesData, float $targetLoad): ?string
//     {
//         $closestService = null;
//         $closestLoad = PHP_FLOAT_MAX;

//         foreach ($citiesData as $city) {
//             foreach ($city['services'] as $service) {
//                 if (isset($service['load']['value'])) {
//                     $serviceLoad = floatval($service['load']['value']);
//                     if ($serviceLoad >= $targetLoad && $serviceLoad < $closestLoad) {
//                         $closestLoad = $serviceLoad;
//                         $closestService = $service['key'];
//                     }
//                 }
//             }
//         }

//         return $closestService;
//     }

//     private function findCityServiceByLoad(array $city, float $targetLoad): ?string
//     {
//         $closestService = null;
//         $closestLoad = PHP_FLOAT_MAX;

//             foreach ($city['services'] as $service) {
//                 if (isset($service['load']['value'])) {
//                     $serviceLoad = floatval($service['load']['value']);
//                     if ($serviceLoad >= $targetLoad && $serviceLoad < $closestLoad) {
//                         $closestLoad = $serviceLoad;
//                         $closestService = $service['key'];
//                     }
//                 }
//             }

//         return $closestService;
//     }

//     // Method to get the status of the city by ID
//     public function retrieve($cityId, $reqMarket = '')
//     {
//         $path = "/v3/cities";
//         $market = $reqMarket ?: $this->client->getMarket();
//         $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path, $market, '', $this->client->getRequestId());

//         try {
//             // Fetch data from the API
//             $response = $this->client->makeRequest('GET', $path, $headers, '', $this->client->isJSONResponse());
            
//             // Validate and process the response
//             if (!$this->isValidResponse($response)) {
//                 return ['error' => 'Invalid API response structure', 'status_code' => 500];
//             }

//             // Transform cities and find the matching city by ID
//             $cities = $this->transformCities($response['data']);
//             $foundCity = $this->findCityById($cities, $cityId);

//             // Return the found city or an error if not found
//             return $foundCity ? ['data' => $foundCity] : ['error' => 'No such city with ID: ' . $cityId, 'status_code' => 404];

//         } catch (Exception $e) {
//             return ['error' => $e->getMessage(), 'status_code' => 500];
//         }
//     }

//     public function getServiceKeyByLoad(float $targetLoad, $cityId = '', $reqMarket = ''): ?array
//     {
//         $path = "/v3/cities";
//         $market = $reqMarket ?: $this->client->getMarket();
//         $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path, $market, '', $this->client->getRequestId());

//         try {
//             // Fetch data from the API
//             $response = $this->fetchCitiesData($path, $headers);
            
//             // Validate the response structure
//             if (!$this->isValidResponse($response)) {
//                 return ['error' => 'Invalid API response structure', 'status_code' => 500];
//             }

//             if ($cityId){
//                 $cities = $this->transformCities($response['data']);
//                 $foundCity = $this->findCityById($cities, $cityId);
//                 return $foundCity ?
//                 ['data' => ['serviceType'=> $this->findCityServiceByLoad($foundCity, $targetLoad)]]:
//                 ['error' => 'No such city with ID: ' . $cityId, 'status_code' => 404];
//             }

//             // Find and return the closest service key by load
//             return ['data' => ['serviceType'=>$this->findClosestServiceByLoad($response['data'], $targetLoad)]];

//         } catch (\Exception $e) {
//             return ['error' => $e->getMessage(), 'status_code' => 500];
//         }
//     }


    // V1.0
    // public function retrieve($reqMarket, $cityId)
    // {
    //     $path = "/v3/cities";
    //     $market = $reqMarket ?: $this->client->getMarket();
    //     $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path, $market, '', $this->client->getRequestId());

    //     try {
    //         // Fetch data from the API
    //         $response = $this->client->makeRequest('GET', $path, '', $headers);
            
    //         if (isset($response['data']) && is_array($response['data'])) {
    //             $cities = $response['data'];
    //             $foundCity = null;

    //             // Iterate over the cities and transform the data
    //             foreach ($cities as &$city) {
    //                 // Transform the locode to id
    //                 if (isset($city['locode'])) {
    //                     $city['id'] = $city['locode'];
    //                     unset($city['locode']);
    //                 }

    //                 // Find the city with the given cityId
    //                 if ($city['id'] === $cityId) {
    //                     $foundCity = $city;
    //                     break;
    //                 }
    //             }

    //             // If the city is found, return its details
    //             return $foundCity ? $foundCity : ['error' => 'No such city with ID: ' . $cityId, 'status_code' => 404];

    //         } else {
    //             // Return error message if the response structure is invalid
    //             return ['error' => 'Invalid API response structure', 'status_code' => 500];
    //         }
    //     } catch (Exception $e) {
    //         // Catch any exceptions and set result as error
    //         return ['error' => $e->getMessage(), 'status_code' => 500];
    //     }
    // }
    
    // public function getKeyByLoad(float $targetLoad, $reqMarket = ''): ?string
    // {
    //     $path = "/v3/cities";
    //     $market = $reqMarket ?: $this->client->getMarket();
    //     $headers = $this->client->getSignatureGenerator()->getHeaders('GET', $path, $market, '', $this->client->getRequestId());

    //     try {
    //         // Fetch data from the API
    //         $response = $this->client->makeRequest('GET', $path, '', $headers);
                
    //         if (isset($response['data']) && is_array($response['data'])) {
    //             $citiesData = $response['data'];
        
    //         $closestService = null;
    //         $closestLoad = PHP_FLOAT_MAX;

    //         // Loop through the data array
    //         foreach ($citiesData['data'] as $city) {
    //             // Loop through the services array for each city
    //             foreach ($city['services'] as $service) {
    //                 // Check if the service has a valid load value
    //                 if (isset($service['load']['value'])) {
    //                     $serviceLoad = floatval($service['load']['value']);
                        
    //                     // If the service load is greater than or equal to the target load and is closer than the previous match
    //                     if ($serviceLoad >= $targetLoad && $serviceLoad < $closestLoad) {
    //                         $closestLoad = $serviceLoad;
    //                         $closestService = $service['key'];
    //                     }
    //                 }
    //             }
    //         }

    //         // Return the key of the closest matching service or null if none found
    //         return $closestService;
            
    //         }  else {
    //             // Return error message if the response structure is invalid
    //             return ['error' => 'Invalid API response structure', 'status_code' => 500];
    //         }
    //     } catch (\Exception $e) {
    //         return ['error' => $e->getMessage(), 'status_code' => 500];
    //     }

    // }

// }

