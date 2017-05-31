<?php

namespace SpryngPaymentsApiPhp;

/**
 * Abstract class for helper classes
 *
 * Class Helper
 * @package SpryngPaymentsApiPhp
 */
abstract class Helper
{
    /**
     * Fills an object from a given json response
     *
     * @param array $jsonObject
     * @return mixed
     */
    abstract public function fill($jsonObject);

    /**
     * Validates if a create request is complete and valid
     *
     * @param array $arguments
     * @return boolean
     */
    abstract public function validateCreateRequestArguments($arguments);
}