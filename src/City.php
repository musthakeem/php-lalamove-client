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
     * @throws \Exception If there is an error fetching the data.
     */
    private function fetchCitiesData(string $path, array $headers)
    {
        $result = $this->client->makeRequest('GET', $path, $headers);
        
        if ($this->client->getConfig()->isJSON()) {
            if (is_string($result)) {
                return json_decode($result, true);
            }
            return $result;
        } else {
            if (is_string($result)) {
                return $result;
            }
            return json_decode(json_encode($result), true);
        }
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
            if (!isset($city['services']) || !is_array($city['services'])) {
                continue;
            }
            
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

        if (!isset($city['services']) || !is_array($city['services'])) {
            return null;
        }

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
     * Prepares a standardized response object.
     *
     * @param array $data The response data.
     * @param bool $isError Whether this is an error response.
     * @param int $statusCode The HTTP status code.
     * @return mixed The formatted response.
     */
    private function prepareResponse(array $data, bool $isError = false, int $statusCode = 200)
    {
        $response = $isError 
            ? array_merge(['error' => true, 'status_code' => $statusCode], $data)
            : ['data' => $data, 'status_code' => $statusCode];
            
        return $this->client->getConfig()->isJSON() 
            ? json_encode($response, JSON_PRETTY_PRINT) 
            : (object) $response;
    }

    /**
     * Retrieves the city data by its ID.
     *
     * @param string $cityId The city ID.
     * @param string $reqMarket Optional market to override the default market.
     * @return mixed The response containing city data or an error message.
     * @throws \InvalidArgumentException If cityId is empty.
     */
    public function retrieve(string $cityId, string $reqMarket = '')
    {
        if (empty(trim($cityId))) {
            throw new \InvalidArgumentException('City ID cannot be empty');
        }

        $path = "/v3/cities";
        $market = $reqMarket ?: $this->client->getMarket();
        $headers = $this->getHeaders('GET', $path, $market);

        try {
            $response = $this->fetchCitiesData($path, $headers);
            
            if (!$this->isValidResponse($response)) {
                return $this->prepareResponse(['message' => 'Invalid API response structure'], true, 500);
            }

            $cities = $this->transformCities($response['data']);
            $foundCity = $this->findCityById($cities, $cityId);

            return $foundCity 
                ? $this->prepareResponse($foundCity) 
                : $this->prepareResponse(['message' => 'No such city with ID: ' . $cityId], true, 404);

        } catch (\Exception $e) {
            return $this->prepareResponse(['message' => $e->getMessage()], true, 500);
        }
    }

    /**
     * Retrieves the service key for the closest matching load.
     *
     * @param float $targetLoad The target load value to match.
     * @param string $cityId Optional city ID to search within a specific city.
     * @param string $reqMarket Optional market to override the default market.
     * @return mixed The service key if found, or an error message.
     * @throws \InvalidArgumentException If targetLoad is less than 0.
     */
    public function getServiceKeyByLoad(float $targetLoad, string $cityId = '', string $reqMarket = '')
    {
        if ($targetLoad < 0) {
            throw new \InvalidArgumentException('Target load cannot be negative');
        }

        $path = "/v3/cities";
        $market = $reqMarket ?: $this->client->getMarket();
        $headers = $this->getHeaders('GET', $path, $market);

        try {
            $response = $this->fetchCitiesData($path, $headers);
            
            if (!$this->isValidResponse($response)) {
                return $this->prepareResponse(['message' => 'Invalid API response structure'], true, 500);
            }

            if (!empty($cityId)) {
                $cities = $this->transformCities($response['data']);
                $foundCity = $this->findCityById($cities, $cityId);
                
                if (!$foundCity) {
                    return $this->prepareResponse(['message' => 'No such city with ID: ' . $cityId], true, 404);
                }
                
                $serviceKey = $this->findCityServiceByLoad($foundCity, $targetLoad);
                return $this->prepareResponse(['serviceType' => $serviceKey]);
            }

            $serviceKey = $this->findClosestServiceByLoad($response['data'], $targetLoad);
            return $this->prepareResponse(['serviceType' => $serviceKey]);

        } catch (\Exception $e) {
            return $this->prepareResponse(['message' => $e->getMessage()], true, 500);
        }
    }
    
    /**
     * Helper method to get request headers with proper authorization.
     *
     * @param string $method The HTTP method.
     * @param string $path The API endpoint path.
     * @param string $market The market to use for this request.
     * @param string $body The request body (if applicable).
     * @return array The prepared headers.
     */
    private function getHeaders(string $method, string $path, string $market, string $body = ''): array
    {
        return $this->client->getSignatureGenerator()->getHeaders(
            $method,
            $path,
            $market,
            $body,
            $this->client->getRequestId()
        );
    }
}

