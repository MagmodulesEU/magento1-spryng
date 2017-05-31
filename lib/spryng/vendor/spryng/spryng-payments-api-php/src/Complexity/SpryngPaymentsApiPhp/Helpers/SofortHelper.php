<?php

namespace SpryngPaymentsApiPhp\Helpers;

use SpryngPaymentsApiPhp\Exception\TransactionException;

/**
 * Class SofortHelper
 * @package SpryngPaymentsApiPhp\Helpers
 */
class SofortHelper
{
    /**
     * Verifies the arguments provided to a SOFORT transaction.
     *
     * @param array $arguments
     * @throws TransactionException
     */
    public static function validateInitiateArguments(array $arguments)
    {
        if (!isset($arguments['account']))
        {
            throw new TransactionException("Account ID is not set.", 203);
        }

        if (!isset($arguments['amount']))
        {
            throw new TransactionException("Amount not provided", 203);
        }

        if (!isset($arguments['customer_ip']))
        {
            throw new TransactionException("Customer IP not provided.", 203);
        }

        if (filter_var($arguments['customer_ip'], FILTER_VALIDATE_IP) === false)
        {
            throw new TransactionException("Customer IP is not a valid IP address.", 204);
        }

        if (!isset($arguments['dynamic_descriptor']))
        {
            throw new TransactionException("Dynamic descriptor not provided.", 203);
        }

        if (!isset($arguments['user_agent']))
        {
            throw new TransactionException("User agent is not provided.", 205);
        }
        else
        {
            if (!is_string($arguments['user_agent']))
            {
                throw new TransactionException("User agent is not a string.", 205);
            }
        }

        if (!isset($arguments['country_code']))
        {
            throw new TransactionException("You did not provide a country code.", 210);
        }

        if (!isset($arguments['details']['redirect_url']))
        {
            throw new TransactionException("Redirect URL is not provided.", 206);
        }
        else
        {
            if (!filter_var($arguments['details']['redirect_url'], FILTER_VALIDATE_URL))
            {
                throw new TransactionException("Redirect URL is not a valid URL.", 207);
            }
        }
    }
}