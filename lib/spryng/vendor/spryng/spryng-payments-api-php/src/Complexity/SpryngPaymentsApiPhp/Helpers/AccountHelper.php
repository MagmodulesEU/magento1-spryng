<?php

namespace SpryngPaymentsApiPhp\Helpers;
use SpryngPaymentsApiPhp\Object\Account;
/**
 * Class AccountHelper
 * @package SpryngPaymentsApiPhp
 */
class AccountHelper
{

    /**
     * @param array $jsonObject
     * @return mixed
     */
    public static function fill($jsonObject)
    {
        $account = new Account();

        foreach($jsonObject as $key => $parameter)
        {
            if ( ! is_array($jsonObject->$key) )
            {
                $account->$key = $parameter;
            }
            else
            {
                switch($key)
                {
                    case 'processors_configurations':
                        foreach($parameter as $configuration)
                        {
                            $type = $configuration->_type;
                            $account->processors_configurations[$type] = $configuration;
                        }
                        break;
                    case 'processors':
                        foreach($parameter as $processor)
                        {
                            array_push($account->processors, $processor);
                        }
                        break;
                }
            }
        }

        return $account;
    }

    /**
     * @param array $arguments
     * @return boolean
     */
    public static function validateCreateRequestArguments($arguments)
    {
        // TODO: Implement validateCreateRequestArguments() method.
    }
}