<?php

/**
 * @license         Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author          Roemer Bakker
 * @copyright       Complexity Software
 */

namespace SpryngPaymentsApiPhp\Object;

/**
 * Class Spryng_Payments_Api_Object_Customer
 * @package SpryngPaymentsApiPhp\Object
 */
class Customer
{
    /**
     * Unique identifier.
     *
     * @var string
     */
    public $_id;

    /**
     * The customers first name.
     *
     * @var string
     */
    public $first_name;

    /**
     * The customers last name.
     *
     * @var string
     */
    public $last_name;

    /**
     * ID of the organisation the customer belongs to.
     *
     * TODO: Make this an organisation object.
     *
     * @var string
     */
    public $organisation;

    /**
     * Array of credit cards associated with this customer.
     *
     * @var array
     */
    public $cards = array();

    /**
     * ISO country code for the customer.
     *
     * @var string
     */
    public $country_code;

    /**
     * Universal region string for the customer like province or state.
     *
     * @var string
     */
    public $locality;

    /**
     * The customers postal code.
     *
     * @var string
     */
    public $postal_code;

    /**
     * Name of the customers street.
     *
     * @var string
     */
    public $street;

    /**
     * Street number in integer form.
     *
     * @var string
     */
    public $street_number;

    /**
     * The customers city.
     *
     * @var string
     */
    public $city;

    /**
     * Customers date of birth.
     *
     * @var string
     */
    public $date_of_birth;

    /**
     * The customers email address.
     *
     * @var string
     */
    public $email_address;

    /**
     * The customers gender. 'male' or 'female'.
     *
     * @var string
     */
    public $gender;

    /**
     * The customers phone number.
     *
     * @var string
     */
    public $phone_number;

    /**
     * Can be used to store a street address with addition.
     *
     * @var string
     */
    public $street_address;

    /**
     * The customers personal title.
     *
     * @var string
     */
    public $title;

    /**
     * The customers active mandates.
     *
     * @var array
     */
    public $mandates = array();
}