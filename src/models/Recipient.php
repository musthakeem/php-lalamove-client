<?php

namespace JM\Lalamove\Models;

use JsonSerializable;
use InvalidArgumentException;

/**
 * Represents the recipient details for a Lalamove stop.
 * This class includes recipient information such as stop ID, name, phone, and optional remarks.
 */
class Recipient implements JsonSerializable
{
    /**
     * @var string The stop ID associated with the recipient.
     */
    private string $stopId;

    /**
     * @var string The recipient's name.
     */
    private string $name;

    /**
     * @var string The recipient's phone number.
     */
    private string $phone;

    /**
     * @var string|null Optional remarks related to the recipient.
     */
    private ?string $remarks = null;

    /**
     * Constructs a new Recipient instance with the provided data.
     *
     * @param array $recipientData The recipient's data, including stopId, name, phone, and optional remarks.
     * @throws InvalidArgumentException if required fields are missing.
     */
    public function __construct(array $recipientData)
    {
        $this->validateRecipientData($recipientData);

        $this->stopId = $recipientData['stopId'];
        $this->name = $recipientData['name'];
        $this->phone = $recipientData['phone'];
        $this->remarks = $recipientData['remarks'] ?? null; // Optional remarks
    }

    /**
     * Validates the recipient data to ensure required fields are provided.
     *
     * @param array $recipientData The recipient data to validate.
     * @throws InvalidArgumentException if any required field (stopId, name, phone) is missing.
     */
    private function validateRecipientData(array $recipientData): void
    {
        if (!isset($recipientData['stopId'], $recipientData['name'], $recipientData['phone'])) {
            throw new InvalidArgumentException("Recipient data must contain stopId, name, and phone.");
        }
    }

    /**
     * Serializes the recipient object to a JSON-friendly format.
     *
     * @return array The recipient data in a JSON-serializable format.
     */
    public function jsonSerialize(): array
    {
        $data = [
            'stopId' => $this->stopId,
            'name' => $this->name,
            'phone' => $this->phone,
        ];

        // Add remarks only if they exist
        if ($this->remarks !== null) {
            $data['remarks'] = $this->remarks;
        }

        return $data;
    }

    // Additional getter methods (optional, based on requirements)
}


// namespace JMusthakeem\Lalamove\Models;
// // Recipient Class
// class Recipient implements \JsonSerializable
// {
//     private string $stopId;
//     private string $name;
//     private string $phone;
//     private ?string $remarks = null; // Optional

//     public function __construct(array $recipientData)
//     {
//         if (!isset($recipientData['stopId'], $recipientData['name'], $recipientData['phone'])) {
//             throw new \Exception("Recipient data must contain stopId, name, and phone.");
//         }

//         $this->stopId = $recipientData['stopId'];
//         $this->name = $recipientData['name'];
//         $this->phone = $recipientData['phone'];

//         // Optional remarks
//         if (isset($recipientData['remarks'])) {
//             $this->remarks = $recipientData['remarks'];
//         }
//     }

//     public function jsonSerialize(): array
//     {
//         $data = [
//             'stopId' => $this->stopId,
//             'name' => $this->name,
//             'phone' => $this->phone,
//         ];

//         // Add remarks only if they exist
//         if ($this->remarks !== null) {
//             $data['remarks'] = $this->remarks;
//         }

//         return $data;
//     }
// }
