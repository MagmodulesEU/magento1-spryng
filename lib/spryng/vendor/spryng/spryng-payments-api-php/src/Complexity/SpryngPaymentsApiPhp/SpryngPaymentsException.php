<?php

namespace SpryngPaymentsApiPhp;

class SpryngPaymentsException extends \Exception
{
    /**
     * @var string
     */
    protected $_field;

    /**
     * Returns the field
     *
     * @return string
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * Sets the field
     *
     * @param $field
     */
    public function setField($field)
    {
        $this->_field = $field;
    }
}