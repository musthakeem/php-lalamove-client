<?php

namespace JM\Lalamove\Payload\Quotation;

use JM\Lalamove\Models\Stop;
use JM\Lalamove\Models\Item;
use DateTime;
use DateTimeZone;
use Exception;

/**
 * Builds a QuotationPayload instance using the builder pattern.
 * Allows for the flexible construction of a QuotationPayload by setting various properties step-by-step.
 */
class QuotationPayloadBuilder
{
    /**
     * @var array The list of stops for the quotation (Array of Stop objects).
     */
    private array $stops;

    /**
     * @var string The service type (e.g., delivery).
     */
    private string $serviceType;

    /**
     * @var string|null The language for the quotation (optional).
     */
    private ?string $language = null;

    /**
     * @var array|null Special requests for the quotation (optional).
     */
    private ?array $specialRequests = null;

    /**
     * @var bool|null Whether the route is optimized (optional).
     */
    private ?bool $isRouteOptimized = null;

    /**
     * @var DateTime|null The scheduled time for the service (optional).
     */
    private ?DateTime $scheduleAt = null;

    /**
     * @var Item|null The item associated with the quotation (optional).
     */
    private ?Item $item = null;

    /**
     * Sets the service type for the quotation.
     *
     * @param string $serviceType The service type.
     * @return self
     */
    public function setServiceType(string $serviceType): self
    {
        $this->serviceType = $serviceType;
        return $this;
    }

    /**
     * Sets the stops for the quotation.
     * Must provide at least 2 stops and no more than 16 stops.
     *
     * @param array $stops The list of stops as arrays.
     * @return self
     * @throws Exception if the number of stops is less than 2 or greater than 16.
     */
    public function setStops(array $stops): self
    {
        if (count($stops) < 2 || count($stops) > 16) {
            throw new Exception("There must be at least 2 stops and no more than 16 stops.");
        }

        // Convert array data into Stop objects
        $this->stops = array_map(fn($stopData) => new Stop($stopData), $stops);
        return $this;
    }

    /**
     * Sets the language for the quotation (optional).
     *
     * @param string $language The language code (e.g., 'en', 'es').
     * @return self
     */
    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Sets the scheduled time for the service (optional).
     * The time must be in a valid datetime format.
     *
     * @param string|null $scheduleAt The scheduled time in string format (e.g., '2024-10-17T10:00:00Z').
     * @return self
     */
    public function setScheduleAt(?string $scheduleAt): self
    {
        if ($scheduleAt) {
            $this->scheduleAt = new DateTime($scheduleAt);
        }
        return $this;
    }

    /**
     * Sets the special requests for the quotation (optional).
     *
     * @param array $specialRequests The array of special requests.
     * @return self
     */
    public function setSpecialRequests(array $specialRequests): self
    {
        $this->specialRequests = $specialRequests;
        return $this;
    }

    /**
     * Sets whether the route should be optimized (optional).
     *
     * @param bool|null $isRouteOptimized Whether route optimization is enabled.
     * @return self
     */
    public function setIsRouteOptimized(?bool $isRouteOptimized): self
    {
        $this->isRouteOptimized = $isRouteOptimized;
        return $this;
    }

    /**
     * Sets the item associated with the quotation.
     *
     * @param array $itemData The item data array.
     * @return self
     */
    public function setItem(array $itemData): self
    {
        $this->item = new Item($itemData);
        return $this;
    }

    /**
     * Builds and returns the QuotationPayload object.
     *
     * @return QuotationPayload The constructed QuotationPayload object.
     * @throws Exception if required fields are not set.
     */
    public function build(): QuotationPayload
    {
        if (!isset($this->serviceType)) {
            throw new Exception("Service Type is required.");
        }

        if (!isset($this->stops)) {
            throw new Exception("Stops are required.");
        }

        return new QuotationPayload($this);
    }

    // Getters used by QuotationPayload

    /**
     * Gets the service type.
     *
     * @return string The service type.
     */
    public function getServiceType(): string
    {
        return $this->serviceType;
    }

    /**
     * Gets the stops as an array of Stop objects.
     *
     * @return array The stops.
     */
    public function getStops(): array
    {
        return $this->stops;
    }

    /**
     * Gets the language.
     *
     * @return string|null The language, or null if not set.
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Gets the special requests.
     *
     * @return array|null The special requests, or null if not set.
     */
    public function getSpecialRequests(): ?array
    {
        return $this->specialRequests;
    }

    /**
     * Gets whether the route is optimized.
     *
     * @return bool|null True if route optimization is enabled, false otherwise.
     */
    public function getIsRouteOptimized(): ?bool
    {
        return $this->isRouteOptimized;
    }

    /**
     * Gets the scheduled time in UTC.
     *
     * @return string|null The scheduled time formatted as UTC, or null if not set.
     */
    public function getScheduleAt(): ?string
    {
        if (!isset($this->scheduleAt)) {
            return null;
        }

        return $this->scheduleAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
    }

    /**
     * Gets the item associated with the quotation.
     *
     * @return Item|null The item, or null if not set.
     */
    public function getItem(): ?Item
    {
        return $this->item;
    }
}


// namespace JMusthakeem\Lalamove\Payload\Quotation;

// use JMusthakeem\Lalamove\Models\Stop;
// use JMusthakeem\Lalamove\Models\Item;



// class QuotationPayloadBuilder
// {
//     private array $stops;
//     private string $serviceType;
//     private ?string $language = null;
//     private ?array $specialRequests = null;
//     private ?bool $isRouteOptimized = null;
//     private ?\DateTime $scheduleAt = null;
//     private ?Item $item = null;

//     public function setServiceType(string $serviceType): self
//     {
//         $this->serviceType = $serviceType;
//         return $this;
//     }

//     public function setStops(array $stops): self
//     {
//         if (count($stops) < 2 || count($stops) > 16) {
//             throw new Exception("There must be at least 2 stops and no more than 16 stops.");
//         }

//         $stopObjects = [];
//         foreach ($stops as $stopData) {
//             $stopObjects[] = new Stop($stopData); // Convert array to Stop objects
//         }

//         $this->stops = $stopObjects;
//         return $this;
//     }

//     public function setLanguage(string $language): self
//     {
//         $this->language = $language;
//         return $this;
//     }

//     public function setScheduleAt(?string $scheduleAt): self
//     {
//         if ($scheduleAt) {
//             $this->scheduleAt = new \DateTime($scheduleAt); // Validate if it's a proper date
//         }
//         return $this;
//     }

//     public function setSpecialRequests(array $specialRequests): self
//     {
//         $this->specialRequests = $specialRequests;
//         return $this;
//     }

//     public function setIsRouteOptimized(?bool $isRouteOptimized): self
//     {
//         $this->isRouteOptimized = $isRouteOptimized;
//         return $this;
//     }

//     public function setItem(array $itemData): self
//     {
//         $this->item = new Item($itemData); // Convert array to Item object
//         return $this;
//     }

//     public function build(): QuotationPayload
//     {
//         if (!isset($this->serviceType)) {
//             throw new Exception("Service Type is required.");
//         }

//         if (!isset($this->stops)) {
//             throw new Exception("Stops are required.");
//         }

//         return new QuotationPayload($this);
//     }

//     public function getServiceType(): string
//     {
//         return $this->serviceType;
//     }

//     public function getStops(): array
//     {
//         return $this->stops;
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

//     public function getScheduleAt(): ?string
//     {
//         if (!isset($this->scheduleAt)) {
//             return $this->scheduleAt;
//         }

//         return $this->scheduleAt->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
//     }

//     public function getItem(): ?Item
//     {
//         return $this->item;
//     }
// }
