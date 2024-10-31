<?php

namespace JM\Lalamove\Payload\Order;

use JM\Lalamove\Models\Sender;
use JM\Lalamove\Models\Metadata;

/**
 * Represents the payload for creating an order in the Lalamove API.
 * This class includes details such as the sender, recipients, and optional fields like proof of delivery (POD) and partner details.
 */
class OrderPayload implements \JsonSerializable
{
    /**
     * @var string The ID of the quotation associated with the order.
     */
    private string $quotationId;

    /**
     * @var Sender The sender details for the order.
     */
    private Sender $sender;

    /**
     * @var array The list of recipients for the order (Array of Recipient objects).
     */
    private array $recipients;

    /**
     * @var bool|null Whether proof of delivery (POD) is enabled for the order (optional).
     */
    private ?bool $isPODEnabled = null;

    /**
     * @var string|null The partner information for the order (optional).
     */
    private ?string $partner = null;

    /**
     * @var Metadata The metadata associated with the order.
     */
    private Metadata $metadata;

    /**
     * Constructs a new OrderPayload instance using the provided builder.
     *
     * @param OrderPayloadBuilder $builder The builder instance used to set the payload data.
     */
    public function __construct(OrderPayloadBuilder $builder)
    {
        $this->quotationId = $builder->getQuotationId();
        $this->sender = $builder->getSender();
        $this->recipients = $builder->getRecipients();
        $this->isPODEnabled = $builder->getIsPODEnabled();
        $this->partner = $builder->getPartner();
        $this->metadata = $builder->getMetadata();
    }

    /**
     * Serializes the OrderPayload object into a JSON-serializable format.
     *
     * @return array The JSON-serializable data.
     */
    public function jsonSerialize(): array
    {
        $data = [
            'quotationId' => $this->quotationId,
            'sender' => $this->sender,
            'recipients' => $this->recipients,
            'metadata' => $this->metadata,
        ];

        // Include optional fields only if they are set
        if ($this->isPODEnabled !== null) {
            $data['isPODEnabled'] = $this->isPODEnabled;
        }

        if ($this->partner !== null) {
            $data['partner'] = $this->partner;
        }

        // Filter out any null values
        return array_filter($data, fn($value) => $value !== null);
    }

    // Add any additional getter methods if needed
}


// namespace JMusthakeem\Lalamove\Payload\Order;

// use JMusthakeem\Lalamove\Models\Sender;
// use JMusthakeem\Lalamove\Models\Metadata;


// class OrderPayload implements \JsonSerializable
// {
//     private string $quotationId;
//     private Sender $sender;
//     private array $recipients; // Now correctly set as an array of Recipient
//     private ?bool $isPODEnabled = null; // Optional
//     private ?string $partner = null;    // Optional
//     private Metadata $metadata;

//     public function __construct(OrderPayloadBuilder $builder) {
//         $this->quotationId = $builder->getQuotationId();
//         $this->sender = $builder->getSender();
//         $this->recipients = $builder->getRecipients();
//         $this->isPODEnabled = $builder->getIsPODEnabled();
//         $this->partner = $builder->getPartner();
//         $this->metadata = $builder->getMetadata();
//     }

//     public function jsonSerialize(): array
//     {
//         $data = [
//             'quotationId' => $this->quotationId,
//             'sender' => $this->sender,
//             'recipients' => $this->recipients,
//             'metadata' => $this->metadata,
//         ];

//         // Add optional fields only if they are set
//         if ($this->isPODEnabled !== null) {
//             $data['isPODEnabled'] = $this->isPODEnabled;
//         }

//         if ($this->partner !== null) {
//             $data['partner'] = $this->partner;
//         }

//         return array_filter($data, fn($value) => $value !== null);
//     }
// }
