<?php

namespace JM\Lalamove\Models;

use JsonSerializable;
use InvalidArgumentException;

/**
 * Represents an item in the Lalamove API.
 * This class handles item properties like quantity, weight, categories, and handling instructions.
 */
class Item implements JsonSerializable
{
    /**
     * @var int|null The quantity of the item.
     */
    private ?string $quantity;

    /**
     * @var string|null The weight of the item.
     */
    private ?string $weight;

    /**
     * @var array|null The categories the item belongs to.
     */
    private ?array $categories;

    /**
     * @var array|null Special handling instructions for the item.
     */
    private ?array $handlingInstructions;

    /**
     * Constructs a new Item instance with the given item data.
     *
     * @param array $itemData The item data containing quantity, weight, categories, and handling instructions.
     * @throws InvalidArgumentException if any required field is missing or invalid.
     */
    public function __construct(array $itemData)
    {
        $this->validateAndSetFields($itemData);
    }

    /**
     * Validates and sets the fields for the item.
     *
     * @param array $itemData The item data to validate and set.
     * @throws InvalidArgumentException if any required field is missing or invalid.
     */
    private function validateAndSetFields(array $itemData): void
    {
        // if (!isset($itemData['quantity']) || is_numeric($itemData['quantity'])) {
        //     throw new InvalidArgumentException("Item must contain a valid quantity string.");
        // }
        // if (!isset($itemData['weight']) || empty($itemData['weight'])) {
        //     throw new InvalidArgumentException("Item must contain a valid weight.");
        // }
        // if (!isset($itemData['categories']) || !is_array($itemData['categories']) || empty($itemData['categories'])) {
        //     throw new InvalidArgumentException("Item must contain at least one category.");
        // }
        // if (!isset($itemData['handlingInstructions']) || !is_array($itemData['handlingInstructions'])) {
        //     throw new InvalidArgumentException("Item must contain valid handling instructions.");
        // }

        $this->quantity = $itemData['quantity'];
        $this->weight = $itemData['weight'];
        $this->categories = $itemData['categories'];
        $this->handlingInstructions = $itemData['handlingInstructions'];
    }

    /**
     * Serializes the item object into a JSON-friendly format.
     *
     * @return array The serialized data.
     */
    public function jsonSerialize(): mixed
    {
        return [
            'quantity' => $this->quantity,
            'weight' => $this->weight,
            'categories' => $this->categories,
            'handlingInstructions' => $this->handlingInstructions,
        ];
    }

    /**
     * Gets the quantity of the item.
     *
     * @return int|null The quantity of the item.
     */
    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    /**
     * Gets the weight of the item.
     *
     * @return string|null The weight of the item.
     */
    public function getWeight(): ?string
    {
        return $this->weight;
    }

    /**
     * Gets the categories of the item.
     *
     * @return array|null The categories of the item.
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }

    /**
     * Gets the handling instructions for the item.
     *
     * @return array|null The handling instructions for the item.
     */
    public function getHandlingInstructions(): ?array
    {
        return $this->handlingInstructions;
    }

    /**
     * Converts the item object to an associative array.
     *
     * @return array The item data as an associative array.
     */
    public function toArray(): array
    {
        return [
            'quantity' => $this->quantity,
            'weight' => $this->weight,
            'categories' => $this->categories,
            'handlingInstructions' => $this->handlingInstructions,
        ];
    }
}

// namespace JMusthakeem\Lalamove\Models;

// class Item implements \JsonSerializable
// {
//     private ?string $quantity;
//     private ?string $weight;
//     private ?array $categories;
//     private ?array $handlingInstructions;

//     public function __construct(array $itemData)
//     {
//         // // Validating and setting quantity
//         // if (!isset($itemData['quantity']) || !is_numeric($itemData['quantity'])) {
//         //     throw new Exception("Item must contain a valid quantity.");
//         // }

//         // // Validating and setting weight
//         // if (!isset($itemData['weight']) || empty($itemData['weight'])) {
//         //     throw new Exception("Item must contain a weight.");
//         // }

//         // // Validating and setting categories
//         // if (!isset($itemData['categories']) || !is_array($itemData['categories']) || empty($itemData['categories'])) {
//         //     throw new Exception("Item must contain at least one category.");
//         // }

//         // // Validating and setting handlingInstructions
//         // if (!isset($itemData['handlingInstructions']) || !is_array($itemData['handlingInstructions']) || empty($itemData['handlingInstructions'])) {
//         //     throw new Exception("Item must contain at least one handling instruction.");
//         // }

//         $this->quantity = (int) $itemData['quantity'];
//         $this->weight = $itemData['weight'];
//         $this->categories = $itemData['categories'];
//         $this->handlingInstructions = $itemData['handlingInstructions'];
//     }

//     // Define how this object should be serialized to JSON
//     public function jsonSerialize(): mixed {
//         return [
//             'quantity' => $this->quantity,
//             'weight' => $this->weight,
//             'categories' => $this->categories,
//             'handlingInstructions' => $this->handlingInstructions,
//         ];
//     }

//     public function getQuantity(): ?int
//     {
//         return $this->quantity;
//     }

//     public function getWeight(): ?string
//     {
//         return $this->weight;
//     }

//     public function getCategories(): ?array
//     {
//         return $this->categories;
//     }

//     public function getHandlingInstructions(): ?array
//     {
//         return $this->handlingInstructions;
//     }

//     public function toArray(): array
//     {
//         return [
//             'quantity' => $this->quantity,
//             'weight' => $this->weight,
//             'categories' => $this->categories,
//             'handlingInstructions' => $this->handlingInstructions,
//         ];
//     }
// }
