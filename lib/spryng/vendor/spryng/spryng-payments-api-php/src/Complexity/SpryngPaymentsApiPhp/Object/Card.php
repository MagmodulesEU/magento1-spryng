<?php

/**
 * @license         Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author          Roemer Bakker
 * @copyright       Complexity Software
 */

namespace SpryngPaymentsApiPhp\Object;

/**
 * Class Spryng_Payments_Api_Object_Card
 * @package SpryngPaymentsApiPhp\Object
 */
class Card
{
    /**
     * The card's unique identifier.
     *
     * @var string
     */
    public $_id;

    /**
     * The The Bank Identification Number.
     *
     * @var string
     */
    public $bin;

    /**
     * The card brand e.g. Mastercard.
     *
     * @var string
     */
    public $brand;

    /**
     * Name of card holder.
     *
     * @var string
     */
    public $cardholder_name;

    /**
     * Indicates the customer the card belongs to.
     *
     * @var Customer
     */
    public $customer;

    /**
     * Card expiry month. Two digits in length.
     *
     * @var integer
     */
    public $expiry_month;

    /**
     * Card expiry year. Two digits in length.
     *
     * @var integer
     */
    public $expiry_year;

    /**
     * Two-letter ISO country code identifying the country of issuance.
     *
     * @var string
     */
    public $issuer_country;

    /**
     * The name of the card issuer.
     *
     * @var string
     */
    public $issuer_name;

    /**
     * Last four digits of the card.
     *
     * @var string
     */
    public $last_four;

    /**
     * The sub-type of the card value is either "credit", "debit" or "prepaid".
     *
     * @var string
     */
    public $type;

    /**
     * Checks to see that a credit card is tied to a valid account and can be successfully charged before storing on
     * the gateway.
     *
     * @var boolean
     */
    public $verified;
}