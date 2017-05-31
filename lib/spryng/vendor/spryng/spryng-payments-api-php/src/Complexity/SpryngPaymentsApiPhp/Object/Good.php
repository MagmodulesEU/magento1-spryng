<?php

namespace SpryngPaymentsApiPhp\Object;

/**
 * Describes a Klarna good.
 *
 * Class Good
 * @package Spryng_PaymentsApiPhp\Object
 */
class Good
{
    /**
     * Good constructor.
     *
     * @param $discount
     * @param array $flags
     * @param int $price
     * @param int $quantity
     * @param string $reference
     * @param string $title
     * @param int $vat
     */
    public function __construct(
        $discount = null,
        array $flags = null,
        $price = null,
        $quantity = null,
        $reference = null,
        $title = null,
        $vat = null
    )
    {
        $this->discount = $discount;
        $this->flags = $flags;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->reference = $reference;
        $this->title = $title;
        $this->vat = $vat;
    }

    /**
     * A discount amount in percentage
     *
     * @var
     */
    public $discount;

    /**
     * A list of flags for this item.
     *
     * Reference:
     *
     * 8: SHIPMENT_FEE: The item actually describes a shipment fee rather than a product.
     * 16: HANDLING_FEE: The item actually describes a handling fee rather than a product.
     * 32: INCL_VAT: The price already includes VAT.
     *
     * @var array
     */
    public $flags = array();

    /**
     * The price of the described good.
     *
     * @var int
     */
    public $price;

    /**
     * The quantity ordered.
     *
     * @var int
     */
    public $quantity;

    /**
     * The internal reference used for this product.
     *
     * @var string
     */
    public $reference;

    /**
     * A human readable title describing the item
     *
     * @var string
     */
    public $title;

    /**
     * The VAT of this item in percentage.
     *
     * @var int
     */
    public $vat;

    public function toJson()
    {
        return json_encode(get_object_vars($this));
    }
}