<?php

namespace SpryngPaymentsApiPhp\Object;

/**
 * Class Organisation
 * @package SpryngPaymentsApiPhp\Object
 */
class Organisation
{
    /**
     * Identifier of the organisation
     *
     * @var string
     */
    public $_id;

    /**
     * Country code for the organisation.
     *
     * @var string <ISO-3166>
     */
    public $country_code;

    /**
     * Email address associated with the organisation.
     *
     * @var string
     */
    public $email;

    /**
     * Optional additional details of an address
     *
     * @var string
     */
    public $extended_address;

    /**
     * If true, invoices will be generated for each account of this organisation every month.
     *
     * @var bool
     */
    public $generate_invoice;

    /**
     * The organisation's locality  / city
     *
     * @var string
     */
    public $locality;

    /**
     * The name of the organisation.
     *
     * @var string
     */
    public $name;

    /**
     * An organisation ID representing the parent of this organisation.
     *
     * @var string
     */
    public $parent_id;

    /**
     * An optional phone number associated with this organisation.
     *
     * @var string
     */
    public $phone;

    /**
     * The organisations postal code
     *
     * @var string
     */
    public $postal_code;

    /**
     * The organisation's country region.
     *
     * @var string
     */
    public $region;

    /**
     * The organisation's street address.
     *
     * @var string
     */
    public $street;

    /**
     * The organisation's street number.
     *
     * @var string
     */
    public $street_number;

    /**
     * The organisation's tax nunber.
     *
     * @var string
     */
    public $tax_number;
}