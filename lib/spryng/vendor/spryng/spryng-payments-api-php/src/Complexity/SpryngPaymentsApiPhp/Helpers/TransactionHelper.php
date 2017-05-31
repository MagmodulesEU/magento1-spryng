<?php

namespace SpryngPaymentsApiPhp\Helpers;

use SpryngPaymentsApiPhp\Exception\TransactionException;
use SpryngPaymentsApiPhp\Object\Account;
use SpryngPaymentsApiPhp\Object\Card;
use SpryngPaymentsApiPhp\Object\Customer;
use SpryngPaymentsApiPhp\Object\Transaction;

class TransactionHelper
{

    static $SPECIAL_PARAMETERS = array(
        'details',
        'account',
        'card',
        'customer'
    );

    /**
     * Takes raw response object and returns formatted transaction object.
     *
     * @param $responseObj
     * @return Transaction
     */
    public static function fillTransaction($responseObj)
    {
        $transaction = new Transaction();

        foreach($responseObj as $key => $parameter)
        {
            if (!in_array($key, self::$SPECIAL_PARAMETERS) )
            {
                $transaction->$key = $parameter;
            }
            else
            {
                switch($key)
                {
                    case 'details':
                        $transaction->details = new \stdClass();
                        $transaction->details = $parameter;
                        break;
                    case 'account':
                        $transaction->account = new Account();
                        $transaction->account->_id = $parameter;
                        break;
                    case 'card':
                        $transaction->card = new Card();
                        $transaction->card->_id = $parameter;
                        break;
                    case 'customer':
                        $transaction->customer = new Customer();
                        $transaction->customer->_id = $parameter;
                }
            }
        }

        return $transaction;
    }

    /**
     * @param $arguments
     * @throws TransactionException
     */
    public static function validateNewTransactionArguments($arguments)
    {
        if (!isset($arguments['account']))
        {
            throw new TransactionException("Account ID is not set.", 203);
        }

        if (!isset($arguments['amount']))
        {
            throw new TransactionException("Amount not provided", 203);
        }

        if (!isset($arguments['card']))
        {
            throw new TransactionException("Card token not provided.", 203);
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

        if (!isset($arguments['payment_product']))
        {
            throw new TransactionException("Payment product not provided.", 203);
        }

        if (isset($arguments['user_agent']))
        {
            if (!is_string($arguments['user_agent']))
            {
                throw new TransactionException("User agent is not a string.", 205);
            }
        }
    }
}