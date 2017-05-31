<?php

/**
 * @license         Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author          Roemer Bakker
 * @copyright       Complexity Software
 */

namespace SpryngPaymentsApiPhp\Object;

/**
 * Class Spryng_Payments_Api_Object_Refund
 * @package SpryngPaymentsApiPhp\Object
 */
class Refund
{
    /**
     * Gateway generated value for identifying individual chargebacks.
     *
     * @var string
     */
    public $_id;

    /**
     * The account object representing the refund.
     *
     * @var Account
     */
    public $account;

    /**
     * Refund amount.
     *
     * @var integer
     */
    public $amount;

    /**
     * Indicates the reason for the refund.
     *
     * @var string
     */
    public $reason;

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

    /**
     * Datetime of which the refund was last updated.
     *
     * @var string
     */
    public $lastStatusUpdate;

    /**
     * Datetime of the moment of creation of this refund
     *
     * @var string
     */
    public $createdAt;
}