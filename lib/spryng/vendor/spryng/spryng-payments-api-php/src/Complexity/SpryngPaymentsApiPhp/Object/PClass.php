<?php

namespace SpryngPaymentsApiPhp\Object;

/**
 * Class PClass
 * @package SpryngPaymentsApiPhp\Object
 */
class PClass
{
    /**
     * The ID of the PClass. This will be passed in your transaction.
     *
     * @var string
     */
    public $_id;

    /**
     * A human readable description of the PClass
     *
     * @var string
     */
    public $description;

    /**
     * Interest rate in percentage * 1000
     *
     * @var integer
     */
    public $interest_rate;

    /**
     * Invoice fee
     *
     * @var int
     */
    public $invoice_fee;

    /**
     * Duration in months
     *
     * @var int
     */
    public $nb_months;

    /**
     * Fee charged at the start of the plan
     *
     * @var int
     */
    public $start_Fee;

    /**
     * Type of the PClass. Can indicate a regular invoice or payment plan.
     *
     * @var int
     */
    public $type;
}