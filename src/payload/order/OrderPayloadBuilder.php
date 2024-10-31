<?php

namespace JM\Lalamove\Payload\Order;

use JM\Lalamove\Models\Sender;
use JM\Lalamove\Models\Metadata;
use JM\Lalamove\Models\Recipient;
use Exception;

/**
 * Builds an OrderPayload object for Lalamove API.
 * Allows setting various properties such as sender, recipients, and metadata, using the builder pattern.
 */
class OrderPayloadBuilder
{
    /**
     * @var string The ID of the quotation associated with the order.
     */
    private string $quotationId;

    /**
     * @var Sender The sender details.
     */
    private Sender $sender;

    /**
     * @var array The list of recipients (Array of Recipient objects).
     */
    private array $recipients = [];

    /**
     * @var bool|null Whether proof of delivery (POD) is enabled (optional).
     */
    private ?bool $isPODEnabled = null;

    /**
     * @var string|null The partner information (optional).
     */
    private ?string $partner = null;

    /**
     * @var Metadata The metadata associated with the order.
     */
    private Metadata $metadata;

    /**
     * Sets the quotation ID for the order.
     *
     * @param string $quotationId The ID of the quotation.
     * @return self
     */
    public function setQuotationId(string $quotationId): self
    {
        $this->quotationId = $quotationId;
        return $this;
    }

    /**
     * Sets the sender details for the order.
     *
     * @param array $senderData The sender data array.
     * @return self
     */
    public function setSender(array $senderData): self
    {
        $this->sender = new Sender($senderData);
        return $this;
    }

    /**
     * Adds recipients to the order.
     * Requires at least 1 recipient and no more than 15 recipients.
     *
     * @param array $recipients The list of recipients as arrays.
     * @return self
     * @throws Exception If there are fewer than 1 or more than 15 recipients.
     */
    public function addRecipients(array $recipients): self
    {
        if (empty($recipients) || count($recipients) > 15) {
            throw new Exception("There must be at least 1 recipient and no more than 15 recipients.");
        }

        // Convert array data into Recipient objects
        $this->recipients = array_map(fn($recipientData) => new Recipient($recipientData), $recipients);

        return $this;
    }

    /**
     * Sets whether proof of delivery (POD) is enabled for the order (optional).
     *
     * @param bool|null $isPODEnabled True if POD is enabled, false otherwise.
     * @return self
     */
    public function setIsPODEnabled(?bool $isPODEnabled): self
    {
        $this->isPODEnabled = $isPODEnabled;
        return $this;
    }

    /**
     * Sets the partner information for the order (optional).
     *
     * @param string|null $partner The partner name or code.
     * @return self
     */
    public function setPartner(?string $partner): self
    {
        $this->partner = $partner;
        return $this;
    }

    /**
     * Sets the metadata for the order.
     *
     * @param array $metadataData The metadata data array.
     * @return self
     */
    public function setMetadata(array $metadataData): self
    {
        $this->metadata = new Metadata($metadataData);
        return $this;
    }

    /**
     * Builds and returns the OrderPayload object.
     *
     * @return OrderPayload The constructed OrderPayload.
     */
    public function build(): OrderPayload
    {
        return new OrderPayload($this);
    }

    // Getters used by OrderPayload

    /**
     * Gets the quotation ID.
     *
     * @return string The quotation ID.
     */
    public function getQuotationId(): string
    {
        return $this->quotationId;
    }

    /**
     * Gets the sender.
     *
     * @return Sender The sender.
     */
    public function getSender(): Sender
    {
        return $this->sender;
    }

    /**
     * Gets the recipients.
     *
     * @return array The list of recipients.
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * Gets whether proof of delivery (POD) is enabled.
     *
     * @return bool|null True if POD is enabled, false otherwise.
     */
    public function getIsPODEnabled(): ?bool
    {
        return $this->isPODEnabled;
    }

    /**
     * Gets the partner information.
     *
     * @return string|null The partner information.
     */
    public function getPartner(): ?string
    {
        return $this->partner;
    }

    /**
     * Gets the metadata for the order.
     *
     * @return Metadata The metadata.
     */
    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }
}


// namespace JMusthakeem\Lalamove\Payload\Order;

// use JMusthakeem\Lalamove\Models\Sender;
// use JMusthakeem\Lalamove\Models\Metadata;
// use JMusthakeem\Lalamove\Models\Recipient;


// class OrderPayloadBuilder
// {
//     private string $quotationId;
//     private Sender $sender;
//     private array $recipients = []; // This remains an array
//     private ?bool $isPODEnabled = null; // Optional
//     private ?string $partner = null;    // Optional
//     private Metadata $metadata;

//     public function setQuotationId(string $quotationId): self
//     {
//         $this->quotationId = $quotationId;
//         return $this;
//     }

//     public function setSender(array $senderData): self
//     {
//         $this->sender = new Sender($senderData);
//         return $this;
//     }

//     public function addRecipients(array $recipients): self
//     {
//         if (empty($recipients) || count($recipients) > 15) {
//             throw new Exception("There must be at least 1 recipient and no more than 15 recipients.");
//         }

//         $recipientObjects = [];
//         foreach ($recipients as $recipientData) {
//             $recipientObjects[] = new Recipient($recipientData); // Convert array to Stop objects
//         }

//         $this->recipients = $recipientObjects;
//         return $this;
//     }

//     public function setIsPODEnabled(?bool $isPODEnabled): self
//     {
//         $this->isPODEnabled = $isPODEnabled;
//         return $this;
//     }

//     public function setPartner(?string $partner): self
//     {
//         $this->partner = $partner;
//         return $this;
//     }

//     public function setMetadata(array $metadataData): self
//     {
//         $this->metadata = new Metadata($metadataData);
//         return $this;
//     }

//     public function build(): OrderPayload
//     {
//         return new OrderPayload($this);
//     }

//     public function getQuotationId(): string
//     {
//         return $this->quotationId;
//     }

//     public function getSender(): Sender
//     {
//         return $this->sender;
//     }
//     public function getRecipients(): array
//     {
//         return $this->recipients;
//     }
//     public function getIsPODEnabled(): bool
//     {
//         return $this->isPODEnabled;
//     }
//     public function getPartner(): string
//     {
//         return $this->partner;
//     }
//     public function getMetadata(): Metadata
//     {
//         return $this->metadata;
//     }
// }
