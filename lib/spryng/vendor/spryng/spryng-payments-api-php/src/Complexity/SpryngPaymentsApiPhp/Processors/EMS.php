<?php

namespace SpryngPaymentsApiPhp\Processors;

use SpryngPaymentsApiPhp\Exception\ProcessorException;
use SpryngPaymentsApiPhp\Object\Transaction;

class EMS implements ProcessorInterface
{
    /**
     * The type/name of the processor
     *
     * @var string
     */
    public $_type;

    /**
     * An array containing the processors configuration parameters
     *
     * @var array
     */
    public $configurations = array();

    /**
     * AbstractProcessor constructor.
     * @param string $_type
     * @param array $configurations
     */
    public function __construct($_type, array $configurations)
    {
        $this->_type = $_type;
        if (!isset($configurations['storename']))
        {
            throw new ProcessorException('Not all processor configuration parameters are provided.', 401);
        }
        $this->configurations = $configurations;
    }

    /**
     * @param string $_type
     * @return void
     */
    public function setType($_type)
    {
        $this->_type = $_type;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setConfiguration($key, $value)
    {
        $this->configurations[$key] = $value;
    }

    /**
     * @param array $configurations
     * @return void
     */
    public function setConfigurations($configurations)
    {
        $this->configurations = $configurations;
    }

    /**
     * @param array $details
     * @return Transaction
     */
    public function initiateTransaction($details)
    {
        // TODO: Implement initiateTransaction() method.
    }
}