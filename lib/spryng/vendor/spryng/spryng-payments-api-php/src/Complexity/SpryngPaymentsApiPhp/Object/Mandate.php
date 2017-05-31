<?php

namespace SpryngPaymentsApiPhp\Object;

/**
 * Class Mandate
 * @package Spryng_PaymentsApiPhp\Object
 */
class Mandate
{

    /**
     * Mandate constructor.
     * @param string $merchant
     * @param string $processor
     * @param string $reference
     * @param string $signed_at
     * @param string $status
     * @param string $_type
     */
    public function __construct($merchant, $processor, $reference, $signed_at, $status, $_type)
    {
        $this->merchant = $merchant;
        $this->processor = $processor;
        $this->reference = $reference;
        $this->signed_at = $signed_at;
        $this->status = $status;
        $this->_type = $_type;
    }

    /**
     * The name of the merchant for which this mandate is valid.
     *
     * @var string
     */
    public $merchant;

    /**
     * The name of the processor for the mandate.
     *
     * @var string
     */
    public $processor;

    /**
     * Unique identifier for the mandate.
     *
     * @var string
     */
    public $reference;

    /**
     * Date and time at which the mandate was signed.
     *
     * @var string
     */
    public $signed_at;

    /**
     * The active status of the mandate.
     *
     * @var string
     */
    public $status;

    /**
     * Additional type for the mandate.
     *
     * @var string
     */
    public $_type;

}