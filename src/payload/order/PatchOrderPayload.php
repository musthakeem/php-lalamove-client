<?php

namespace JM\Lalamove\Payload\Order;

use Exception;
use JsonSerializable;

/**
 * Represents the payload for patching an order in the Lalamove API.
 * This payload contains an array of stops that must be validated before being used in the API request.
 */
class PatchOrderPayload implements JsonSerializable
{
    /**
     * @var PatchOrderStop[] Array of stops for the payload.
     */
    protected array $stops;

    /**
     * Constructs the PatchOrderPayload object using the provided builder.
     *
     * @param PatchOrderPayloadBuilder $builder The builder instance used to create the payload.
     * @throws Exception If the number of stops is not between 2 and 17, or if any stop has an empty address.
     */
    public function __construct(PatchOrderPayloadBuilder $builder)
    {
        $stops = $builder->getStops();
        $stopsLength = count($stops);

        // Validate the number of stops (must be between 2 and 17)
        if ($stopsLength > 17 || $stopsLength < 2) {
            throw new Exception("The number of stops must be between 2 and 17.");
        }

        // Validate that all stops have a non-empty address
        foreach ($stops as $stop) {
            if (empty($stop['address'])) {
                throw new Exception("Each stop must contain a valid, non-empty address.");
            }
        }

        $this->stops = $stops;
    }

    /**
     * Gets the array of stops for this payload.
     *
     * @return PatchOrderStop[] The array of stops.
     */
    public function getStops(): array
    {
        return $this->stops;
    }

    /**
     * Serializes the PatchOrderPayload to a JSON-friendly format.
     *
     * @return array The serialized data.
     */
    public function jsonSerialize(): array
    {
        return array_filter($this->stops, fn($value) => $value !== null);
    }
}



// namespace JMusthakeem\Lalamove\Payload\Order;

// class PatchOrderPayload implements \JsonSerializable
// {
//     /**
//      * @var PatchOrderStop[] Array of stops for the payload
//      */
//     protected $stops;

//     /**
//      * Constructor for PatchOrderPayload using the Builder.
//      *
//      * @param PatchOrderPayloadBuilder $builder The builder used to construct this payload.
//      *
//      * @throws \Exception If the number of stops is invalid or if any stop has an empty address.
//      */
//     public function __construct(PatchOrderPayloadBuilder $builder)
//     {
//         $stops = $builder->getStops();
//         // print_r(json_encode($stops,JSON_PRETTY_PRINT));
//         $stopsLength = count($stops);

//         // Validate the number of stops (must be between 2 and 17)
//         if ($stopsLength > 17 || $stopsLength < 2) {
//             throw new \Exception("Stops must be between 2 and 17");
//         }

//         // Validate that all stops have an address
//         foreach ($stops as $stop) {
//             // print_r(json_encode($stop,JSON_PRETTY_PRINT));
//             if (empty($stop['address'])) {
//                 throw new \Exception("Address cannot be empty");
//             }
//         }

//         $this->stops = $stops;
//     }

//     /**
//      * Gets the stops for this payload.
//      *
//      * @return PatchOrderStop[]
//      */
//     public function getStops()
//     {
//         return $this->stops;
//     }

//     public function jsonSerialize(): array
//     {
//         $data = $this->stops;

//         return array_filter($data, fn($value) => $value !== null);
//     }
// }
