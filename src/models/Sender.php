<?php

namespace JM\Lalamove\Models;

use JsonSerializable;
use InvalidArgumentException;

/**
 * Represents the sender details for a Lalamove stop.
 * This class includes sender information such as stop ID, name, and phone number.
 */
class Sender implements JsonSerializable
{
    /**
     * @var string The stop ID associated with the sender.
     */
    private string $stopId;

    /**
     * @var string The sender's name.
     */
    private string $name;

    /**
     * @var string The sender's phone number.
     */
    private string $phone;

    /**
     * Constructs a new Sender instance with the provided data.
     *
     * @param array $senderData The sender's data, including stopId, name, and phone.
     * @throws InvalidArgumentException if required fields are missing.
     */
    public function __construct(array $senderData)
    {
        $this->validateSenderData($senderData);

        $this->stopId = $senderData['stopId'];
        $this->name = $senderData['name'];
        $this->phone = $senderData['phone'];
    }

    /**
     * Validates the sender data to ensure required fields are provided.
     *
     * @param array $senderData The sender data to validate.
     * @throws InvalidArgumentException if any required field (stopId, name, phone) is missing.
     */
    private function validateSenderData(array $senderData): void
    {
        if (!isset($senderData['stopId'], $senderData['name'], $senderData['phone'])) {
            throw new InvalidArgumentException("Sender data must contain stopId, name, and phone.");
        }
    }

    /**
     * Serializes the sender object to a JSON-friendly format.
     *
     * @return array The sender data in a JSON-serializable format.
     */
    public function jsonSerialize(): array
    {
        return [
            'stopId' => $this->stopId,
            'name' => $this->name,
            'phone' => $this->phone,
        ];
    }
}


// namespace JMusthakeem\Lalamove\Models;

// class Sender implements \JsonSerializable
// {
//     private string $stopId;
//     private string $name;
//     private string $phone;

//     public function __construct(array $senderData)
//     {
//         if (!isset($senderData['stopId'], $senderData['name'], $senderData['phone'])) {
//             throw new \Exception("Sender data must contain stopId, name, and phone.");
//         }

//         $this->stopId = $senderData['stopId'];
//         $this->name = $senderData['name'];
//         $this->phone = $senderData['phone'];
//     }

//     public function jsonSerialize(): array
//     {
//         return [
//             'stopId' => $this->stopId,
//             'name' => $this->name,
//             'phone' => $this->phone,
//         ];
//     }
// }