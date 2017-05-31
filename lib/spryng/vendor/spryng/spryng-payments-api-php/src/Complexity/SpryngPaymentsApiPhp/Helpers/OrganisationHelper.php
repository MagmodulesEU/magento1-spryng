<?php

namespace SpryngPaymentsApiPhp\Helpers;

use SpryngPaymentsApiPhp\Object\Organisation;

class OrganisationHelper
{

    /**
     * Fills an object from a given json response
     *
     * @param array $jsonObject
     * @return mixed
     */
    public static function fill($jsonObject)
    {
        $organisation = new Organisation();

        foreach ($jsonObject as $key => $parameter)
        {
            $organisation->$key = $parameter;
        }

        return $organisation;
    }

    /**
     * Validates if a create request is complete and valid
     *
     * @param array $arguments
     * @return boolean
     */
    public static function validateCreateRequestArguments($arguments)
    {
        // TODO: Implement validateCreateRequestArguments() method.
    }
}