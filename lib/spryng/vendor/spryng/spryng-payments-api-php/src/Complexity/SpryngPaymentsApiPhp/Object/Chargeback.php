<?php

/**
 * @license         Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author          Roemer Bakker
 * @copyright       Complexity Software
 */

namespace SpryngPaymentsApiPhp\Object;

/**
 * Class Spryng_Payments_Api_Object_Chargeback
 * @package SpryngPaymentsApiPhp\Object
 */
class Chargeback
{
    /**
     * Gateway generated value for identifying individual chargebacks.
     *
     * @var string
     */
    public $_id;

    /**
     * The account object to which the chargeback relates.
     *
     * @var Account
     */
    public $account;

    /**
     * Indicates the reason for the chargeback.
     *
     * @var string
     */
    public $reason_code;

    /**
     * A list of file ids representing dispute files.
     *
     * @var array
     */
    public $Files;

    /**
     * The latest status of the chargeback.
     *
     * @var string
     */
    public $status;

    /**
     * Object representing the original transaction.
     *
     * @var Transaction
     */
    public $transaction;
}