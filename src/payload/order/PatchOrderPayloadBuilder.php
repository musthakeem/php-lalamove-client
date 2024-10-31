<?php

namespace JM\Lalamove\Payload\Order;

use Exception;

/**
 * Builder class for constructing a PatchOrderPayload object for the Lalamove API.
 * Allows setting stops and validating them before constructing the final payload.
 */
class PatchOrderPayloadBuilder
{
    /**
     * @var PatchOrderStop[] Array of stops to build the payload.
     */
    protected array $stops = [];

    /**
     * Initializes a new instance of PatchOrderPayloadBuilder.
     *
     * @return PatchOrderPayloadBuilder The builder instance.
     */
    public static function patchOrderPayload(): PatchOrderPayloadBuilder
    {
        return new self();
    }

    /**
     * Sets the stops for the payload.
     * 
     * @param PatchOrderStop[] $stops Array of PatchOrderStop instances.
     * @return PatchOrderPayloadBuilder The current builder instance.
     */
    public function withStops(array $stops): PatchOrderPayloadBuilder
    {
        $this->stops = $stops;
        return $this;
    }

    /**
     * Builds the PatchOrderPayload object.
     * 
     * @return PatchOrderPayload The constructed PatchOrderPayload object.
     * @throws Exception If validation fails (handled in the PatchOrderPayload constructor).
     */
    public function build(): PatchOrderPayload
    {
        return new PatchOrderPayload($this);
    }

    /**
     * Gets the stops that have been set for the payload.
     * 
     * @return PatchOrderStop[] The array of PatchOrderStop instances.
     */
    public function getStops(): array
    {
        return $this->stops;
    }
}


// namespace JMusthakeem\Lalamove\Payload\Order;

// class PatchOrderPayloadBuilder
// {
//     /**
//      * @var PatchOrderStop[] Array of stops to build the payload
//      */
//     protected $stops = [];

//     /**
//      * Static method to initialize the builder.
//      *
//      * @return PatchOrderPayloadBuilder
//      */
//     public static function patchOrderPayload(): PatchOrderPayloadBuilder
//     {
//         return new self();
//     }

//     /**
//      * Sets the stops for the builder.
//      *
//      * @param PatchOrderStop[] $stops Array of PatchOrderStop instances.
//      *
//      * @return PatchOrderPayloadBuilder
//      */
//     public function withStops(array $stops): PatchOrderPayloadBuilder
//     {
//         $this->stops = $stops;
//         return $this;
//     }

//     /**
//      * Builds the PatchOrderPayload object.
//      *
//      * @return PatchOrderPayload
//      * @throws \Exception If validation fails.
//      */
//     public function build(): PatchOrderPayload
//     {
//         return new PatchOrderPayload($this);
//     }

//     /**
//      * Returns the stops set in the builder.
//      *
//      * @return PatchOrderStop[]
//      */
//     public function getStops(): array
//     {
//         return $this->stops;
//     }
// }
