<?php

namespace JM\Lalamove\Models;

use JsonSerializable;
use InvalidArgumentException;

/**
 * Represents the metadata for Lalamove operations.
 * This class allows handling of metadata and ensures it's not empty.
 */
class Metadata implements JsonSerializable
{
    /**
     * @var array The metadata content.
     */
    private array $metadata = [];

    /**
     * Constructs a new Metadata instance with the provided data.
     *
     * @param array $metadataData The metadata content as an associative array.
     * @throws InvalidArgumentException if the metadata is empty.
     */
    public function __construct(array $metadataData)
    {
        if (empty($metadataData)) {
            throw new InvalidArgumentException("Metadata cannot be empty.");
        }

        $this->metadata = $metadataData;
    }

    /**
     * Serializes the metadata for JSON representation.
     *
     * @return array The metadata in a JSON-serializable format.
     */
    public function jsonSerialize(): array
    {
        return $this->metadata;
    }
}


// namespace JMusthakeem\Lalamove\Models;

// // Metadata Class
// class Metadata implements \JsonSerializable
// {
//     private array $metadata = [];

//     public function __construct(array $metadataData)
//     {
//         if (empty($metadataData)) {
//             throw new \Exception("Metadata cannot be empty.");
//         }

//         $this->metadata = $metadataData;
//     }

//     public function jsonSerialize(): array
//     {
//         return $this->metadata;
//     }
// }



// class Metadata implements \JsonSerializable
// {
//     private string $restaurantOrderId;
//     private string $restaurantName;

//     public function __construct(array $metadataData)
//     {
//         if (!isset($metadataData['restaurantOrderId'], $metadataData['restaurantName'])) {
//             throw new \Exception("Metadata must contain restaurantOrderId and restaurantName.");
//         }

//         $this->restaurantOrderId = $metadataData['restaurantOrderId'];
//         $this->restaurantName = $metadataData['restaurantName'];
//     }

//     public function jsonSerialize(): array
//     {
//         return [
//             'restaurantOrderId' => $this->restaurantOrderId,
//             'restaurantName' => $this->restaurantName,
//         ];
//     }
// }
