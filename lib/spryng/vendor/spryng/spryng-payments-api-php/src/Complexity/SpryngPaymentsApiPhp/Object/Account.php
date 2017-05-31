<?php

/**
 * @license         Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author          Roemer Bakker
 * @copyright       Complexity Software
 */

namespace SpryngPaymentsApiPhp\Object;

/**
 * Class Account
 * @package SpryngPaymentsApiPhp\Object
 */
class Account
{
    /**
     * The unique ID identifying the account
     *
     * @var string
     */
    public $_id;

    /**
     * The ID of the organisation the account belongs to
     *
     * @var string
     */
    public $organisation;

    /**
     * The name of the account
     *
     * @var string
     */
    public $name;

    /**
     * The account's currency
     *
     * @var string <ISO 4217>
     */
    public $currency_code;

    /**
     * Array containing the configuration parameters of it's processors
     *
     * @var array
     */
    public $processors_configurations = array();

    /**
     * Array containing the account's processors
     *
     * @var array
     */
    public $processors = array();

    /**
     * Monthly fee for the account
     *
     * @var int
     */
    public $monthly_fee;

    /**
     * A list of fee descriptions for each available payment method
     *
     * @var array
     */
    public $service_fees = array();

    /**
     * A description of the account
     *
     * @var string
     */
    public $description;

    /**
     * Routing ruleset for the account
     *
     * @var string
     */
    public $route_rule_set;

    /**
     * A webhook URL that is called when a chargeback is updated
     *
     * @var string
     */
    public $webhook_chargeback_update;

    /**
     * A webhook url that is called when a refund is updated
     *
     * @var string
     */
    public $webhook_refund_update;

    /**
     * A webhook url that is called when a transaction is updated
     *
     * @var string
     */
    public $webhook_transaction_update;
}