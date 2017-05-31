<?php

namespace SpryngPaymentsApiPhp\Helpers;

use SpryngPaymentsApiPhp\Exception\TransactionException;

class BancontactHelper
{
    public static function validateInitiateArguments(array $arguments)
    {
        try
        {
            TransactionHelper::validateNewTransactionArguments($arguments);
        }
        catch (TransactionException $exception)
        {
            throw $exception;
        }

        if (!isset($arguments['details']['redirect_url']))
        {
            throw new TransactionException('Transaction not valid. details.redirect_url not set.', 201);
        }
    }
}