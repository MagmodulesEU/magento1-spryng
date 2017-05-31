<?php

/**
 * @license         Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author          Roemer Bakker
 * @copyright       Complexity Software
 */

namespace SpryngPaymentsApiPhp\Object;

/**
 * Class Spryng_Payments_Api_Object_Customer_Address
 * @package SpryngPaymentsApiPhp\Object
 */
class Customer_Address
{
    /**
     * A customer can have many address objects. e.g. Shipping, Billing, Office or Home.
     *
     * @var string
     */
    public $description;

    /**
     * Street address of the customer.
     *
     * @var string
     */
    public $street_address;

    /**
     * Region of the customer e.g. state or province.
     *
     * @var string
     */
    public $region;

    /**
     * Locality of the customer e.g. city or town.
     *
     * @var string
     */
    public $locality;

    /**
     * Postal code of the customer.
     *
     * @var string
     */
    public $postal_code;

    /**
     * Available for additional address details.
     *
     * @var string
     */
    public $extended_address;

    /**
     * Two character ISO country code.
     *
     * @var string
     */
    public $country_code;
}