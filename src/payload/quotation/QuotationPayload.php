<?php

namespace JM\Lalamove\Payload\Quotation;

use JM\Lalamove\Models\Item;
use JsonSerializable;

/**
 * Represents the payload for creating a quotation in the Lalamove API.
 * This class is built using the QuotationPayloadBuilder and contains details such as stops, service type, language, etc.
 */
class QuotationPayload implements JsonSerializable
{
    /**
     * @var array The list of stops for the quotation (Array of Stop objects).
     */
    private array $stops;

    /**
     * @var string The type of service being requested (e.g., delivery).
     */
    private string $serviceType;

    /**
     * @var string|null The language for the quotation (optional).
     */
    private ?string $language;

    /**
     * @var array|null Special requests for the quotation (optional).
     */
    private ?array $specialRequests;

    /**
     * @var bool|null Whether the route should be optimized (optional).
     */
    private ?bool $isRouteOptimized;

    /**
     * @var string|null The scheduled time for the service (optional).
     */
    private ?string $scheduleAt;

    /**
     * @var Item|null The item associated with the quotation (optional).
     */
    private ?Item $item;

    /**
     * Constructs a QuotationPayload instance using the provided builder.
     *
     * @param QuotationPayloadBuilder $builder The builder instance used to set the payload data.
     */
    public function __construct(QuotationPayloadBuilder $builder)
    {
        $this->stops = $builder->getStops();
        $this->serviceType = $builder->getServiceType();
        $this->language = $builder->getLanguage();
        $this->specialRequests = $builder->getSpecialRequests();
        $this->isRouteOptimized = $builder->getIsRouteOptimized();
        $this->scheduleAt = $builder->getScheduleAt();
        $this->item = $builder->getItem();
    }

    /**
     * Serializes the QuotationPayload object into a JSON-serializable format.
     *
     * @return array The JSON-serializable data.
     */
    public function jsonSerialize(): array
    {
        $data = [
            'scheduleAt' => $this->scheduleAt,
            'serviceType' => $this->serviceType,
            'specialRequests' => $this->specialRequests,
            'language' => $this->language,
            'stops' => $this->stops,
            'isRouteOptimized' => $this->isRouteOptimized,
            'item' => $this->item,
        ];

        // Remove null values from the data
        return array_filter($data, fn($value) => $value !== null);
    }

    /**
     * Gets the stops for the quotation.
     *
     * @return array The array of stops.
     */
    public function getStops(): array
    {
        return $this->stops;
    }

    /**
     * Gets the service type for the quotation.
     *
     * @return string The service type (e.g., delivery).
     */
    public function getServiceType(): string
    {
        return $this->serviceType;
    }

    /**
     * Gets the language for the quotation.
     *
     * @return string|null The language, or null if not specified.
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Gets the special requests for the quotation.
     *
     * @return array|null The special requests, or null if not specified.
     */
    public function getSpecialRequests(): ?array
    {
        return $this->specialRequests;
    }

    /**
     * Gets whether the route is optimized for the quotation.
     *
     * @return bool|null True if route optimization is enabled, false otherwise.
     */
    public function getIsRouteOptimized(): ?bool
    {
        return $this->isRouteOptimized;
    }

    /**
     * Gets the scheduled time for the service.
     *
     * @return string|null The scheduled time, or null if not specified.
     */
    public function getScheduleAt(): ?string
    {
        return $this->scheduleAt;
    }

    /**
     * Gets the item associated with the quotation.
     *
     * @return Item|null The item, or null if not specified.
     */
    public function getItem(): ?Item
    {
        return $this->item;
    }
}


// namespace JMusthakeem\Lalamove\Payload\Quotation;

// use JMusthakeem\Lalamove\Models\Item;

// class QuotationPayload implements \JsonSerializable
// {
//     private array $stops;
//     private string $serviceType;
//     private ?string $language;
//     private ?array $specialRequests;
//     private ?bool $isRouteOptimized;
//     private ?string $scheduleAt;
//     private ?Item $item;

//     public function __construct(QuotationPayloadBuilder $builder)
//     {
//         $this->stops = $builder->getStops(); // Array of Stop objects
//         $this->serviceType = $builder->getServiceType();
//         $this->language = $builder->getLanguage();
//         $this->specialRequests = $builder->getSpecialRequests();
//         $this->isRouteOptimized = $builder->getIsRouteOptimized();
//         $this->scheduleAt = $builder->getScheduleAt();

        
//         $this->item = $builder->getItem();
//     }

//      // Define how this object should be serialized to JSON
//     public function jsonSerialize(): mixed {
//         // Build the array to be serialized

//         $data = [
//             'scheduleAt' => $this->scheduleAt,
//             'serviceType' => $this->serviceType,
//             'specialRequests' => $this->specialRequests,
//             'language' => $this->language,
//             'stops' => $this->stops,
//             'isRouteOptimized' => $this->isRouteOptimized,
//             'item' => $this->item,
//         ];

//         // Filter out null values
//         return array_filter($data, fn($value) => $value !== null);

//     }

//     public function getStops(): array
//     {
//         return $this->stops;
//     }

//     public function getServiceType(): string
//     {
//         return $this->serviceType;
//     }

//     public function getLanguage(): ?string
//     {
//         return $this->language;
//     }

//     public function getSpecialRequests(): ?array
//     {
//         return $this->specialRequests;
//     }

//     public function getIsRouteOptimized(): ?bool
//     {
//         return $this->isRouteOptimized;
//     }

//     public function getScheduleAt(): ?\DateTime
//     {
//         return $this->scheduleAt;
//     }

//     public function getItem(): ?Item
//     {
//         return $this->item;
//     }
// }
