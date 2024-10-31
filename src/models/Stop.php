<?php

namespace JM\Lalamove\Models;

use JsonSerializable;
use InvalidArgumentException;

/**
 * Represents a stop in the Lalamove system, containing location and address information.
 */
class Stop implements JsonSerializable
{
    /**
     * @var string The latitude of the stop.
     */
    private string $lat;

    /**
     * @var string The longitude of the stop.
     */
    private string $long;

    /**
     * @var string The address of the stop.
     */
    private string $address;

    /**
     * @var array The coordinates of the stop, including latitude and longitude.
     */
    private array $coordinates;

    /**
     * @var string|null An optional stop ID for the stop.
     */
    private ?string $stopId = null;

    /**
     * Constructs a new Stop instance with the provided data.
     *
     * @param array $stopData The stop data, including coordinates (lat, lng) and address, and optionally stopId.
     * @throws InvalidArgumentException if required fields are missing or invalid.
     */
    public function __construct(array $stopData)
    {
        $this->validateStopData($stopData);

        $this->lat = $stopData['coordinates']['lat'];
        $this->long = $stopData['coordinates']['lng'];
        $this->coordinates = $stopData['coordinates'];
        $this->address = $stopData['address'];

        // Optionally set stopId if it exists
        $this->stopId = $stopData['stopId'] ?? null;
    }

    /**
     * Validates the stop data to ensure required fields are provided.
     *
     * @param array $stopData The stop data to validate.
     * @throws InvalidArgumentException if coordinates or address are missing.
     */
    private function validateStopData(array $stopData): void
    {
        if (!isset($stopData['coordinates']['lat'], $stopData['coordinates']['lng'])) {
            throw new InvalidArgumentException("Each stop must contain valid coordinates (lat and lng).");
        }

        if (!isset($stopData['address']) || empty($stopData['address'])) {
            throw new InvalidArgumentException("Each stop must contain an address.");
        }
    }

    /**
     * Serializes the stop object to a JSON-friendly format.
     *
     * @return array The stop data in a JSON-serializable format.
     */
    public function jsonSerialize(): array
    {
        $data = [
            'coordinates' => $this->coordinates,
            'address' => $this->address,
        ];

        if ($this->stopId !== null) {
            $data['stopId'] = $this->stopId;
        }

        return $data;
    }

    /**
     * Gets the latitude of the stop.
     *
     * @return string The latitude of the stop.
     */
    public function getLatitude(): string
    {
        return $this->lat;
    }

    /**
     * Gets the longitude of the stop.
     *
     * @return string The longitude of the stop.
     */
    public function getLongitude(): string
    {
        return $this->long;
    }

    /**
     * Gets the address of the stop.
     *
     * @return string The address of the stop.
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * Converts the stop object to an associative array.
     *
     * @return array The stop data as an associative array.
     */
    public function toArray(): array
    {
        $data = [
            'coordinates' => [
                'lat' => $this->lat,
                'lng' => $this->long,
            ],
            'address' => $this->address,
        ];

        if ($this->stopId !== null) {
            $data['stopId'] = $this->stopId;
        }

        return $data;
    }
}



// namespace JMusthakeem\Lalamove\Models;

// class Stop implements \JsonSerializable
// {
//     private string $lat;
//     private string $long;
//     private string $address;
//     private array $coordinates;
//     private ?string $stopId = null; // Optional stopId, initialized to null

//     public function __construct(array $stopData)
//     {
//         if (!isset($stopData['coordinates']['lat']) || !isset($stopData['coordinates']['lng'])) {
//             throw new \Exception("Each stop must contain valid coordinates (lat and lng).");
//         }

//         if (!isset($stopData['address']) || empty($stopData['address'])) {
//             throw new \Exception("Each stop must contain an address.");
//         }

//         $this->lat = $stopData['coordinates']['lat'];
//         $this->long = $stopData['coordinates']['lng'];
//         $this->coordinates = $stopData['coordinates'];
//         $this->address = $stopData['address'];

//         // Optionally set stopId if it exists in stopData
//         if (isset($stopData['stopId'])) {
//             $this->stopId = $stopData['stopId'];
//         }
//     }

//     // Define how this object should be serialized to JSON
//     public function jsonSerialize(): mixed
//     {
//         // Base structure
//         $data = [
//             'coordinates' => $this->coordinates,
//             'address' => $this->address,
//         ];

//         // Add stopId only if it exists
//         if ($this->stopId !== null) {
//             $data['stopId'] = $this->stopId;
//         }

//         return $data;
//     }

//     public function getLatitude(): string
//     {
//         return $this->lat;
//     }

//     public function getLongitude(): string
//     {
//         return $this->long;
//     }

//     public function getAddress(): string
//     {
//         return $this->address;
//     }

//     public function toArray(): array
//     {
//         $data = [
//             'coordinates' => [
//                 'lat' => $this->lat,
//                 'lng' => $this->long,
//             ],
//             'address' => $this->address,
//         ];

//         // Add stopId only if it exists
//         if ($this->stopId !== null) {
//             $data['stopId'] = $this->stopId;
//         }

//         return $data;
//     }
// }


// class Stop implements \JsonSerializable
// {
//     private string $lat;
//     private string $long;
//     private string $address;
//     private array $coordinates;

//     public function __construct(array $stopData)
//     {
//         if (!isset($stopData['coordinates']['lat']) || !isset($stopData['coordinates']['lng'])) {
//             throw new Exception("Each stop must contain valid coordinates (lat and lng).");
//         }

//         if (!isset($stopData['address']) || empty($stopData['address'])) {
//             throw new Exception("Each stop must contain an address.");
//         }

//         $this->lat = $stopData['coordinates']['lat'];
//         $this->long = $stopData['coordinates']['lng'];
//         $this->coordinates = $stopData['coordinates'];
//         $this->address = $stopData['address'];
//     }

//     // Define how this object should be serialized to JSON
//     public function jsonSerialize(): mixed {
//         return [
//             'coordinates' => $this->coordinates,
//             'address' => $this->address,
//         ];
//     }

//     public function getLatitude(): string
//     {
//         return $this->lat;
//     }

//     public function getLongitude(): string
//     {
//         return $this->long;
//     }

//     public function getAddress(): string
//     {
//         return $this->address;
//     }

//     public function toArray(): array
//     {
//         return [
//             'coordinates' => [
//                 'lat' => $this->latitude,
//                 'lng' => $this->longitude,
//             ],
//             'address' => $this->address,
//         ];
//     }
// }
