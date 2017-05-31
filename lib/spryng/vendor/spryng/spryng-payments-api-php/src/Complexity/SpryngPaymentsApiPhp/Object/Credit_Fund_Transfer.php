<?php

/**
 * @license         Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author          Roemer Bakker
 * @copyright       Complexity Software
 */

namespace SpryngPaymentsApiPhp\Object;

/**
 * Class Spryng_Payments_Api_Object_Credit_Fund_Transfer
 * @package SpryngPaymentsApiPhp\Object
 */
class Credit_Fund_Transfer
{
    /**
     * @var
     */
    public $_id;

    /**
     * @var
     */
    public $account;

    /**
     * @var
     */
    public $amount;

    /**
     * @var
     */
    public $authorization_code;

    /**
     * @var
     */
    public $card;

    /**
     * @var
     */
    public $country_code;

    /**
     * @var
     */
    public $created_at;

    /**
     * @var
     */
    public $customer;

    /**
     * @var
     */
    public $customer_ip;

    /**
     * @var
     */
    public $cvv_present;

    /**
     * @var
     */
    public $cvv_response;

    /**
     * @var
     */
    public $dynamic_descriptor;

    /**
     * @var
     */
    public $geo_location;

    /**
     * @var
     */
    public $merchant_reference;

    /**
     * @var
     */
    public $payment_product_type;

    /**
     * @var
     */
    public $stan;

    /**
     * @var
     */
    public $status;

    /**
     * @var
     */
    public $user_agent;
}