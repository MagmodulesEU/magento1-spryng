<?php

namespace SpryngPaymentsApiPhp\Processors;

use SpryngPaymentsApiPhp\Object\Transaction;

interface ProcessorInterface
{
    /**
     * @param string $_type
     * @return void
     */
    public function setType($_type);

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setConfiguration($key, $value);

    /**
     * @param array $configurations
     * @return void
     */
    public function setConfigurations($configurations);

    /**
     * @param array $details
     * @return Transaction
     */
    public function initiateTransaction($details);
}