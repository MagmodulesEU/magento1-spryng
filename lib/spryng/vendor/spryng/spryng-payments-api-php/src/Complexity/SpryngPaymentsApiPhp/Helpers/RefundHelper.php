<?php

namespace SpryngPaymentsApiPhp\Helpers;

use SpryngPaymentsApiPhp\Object\Account;
use SpryngPaymentsApiPhp\Object\Refund;
use SpryngPaymentsApiPhp\Object\Transaction;

class RefundHelper
{
    static $SPECIAL_PARAMETERS = array(
        'transaction', 'account'
    );

    public static function fillRefundObject($response)
    {
        $refund = new Refund();

        foreach($response as $key => $parameter)
        {
            if (!in_array($key, self::$SPECIAL_PARAMETERS))
            {
                $refund->$key = $parameter;
            }
            else
            {
                switch($key)
                {
                    case 'transaction':
                        $transaction = new Transaction();
                        $transaction->_id = $response->$key;
                        $refund->transaction = $transaction;
                        break;
                    case 'account':
                        $account = new Account();
                        $account->_id = $response->$key;
                        $refund->account = $account;
                        break;
                }
            }
        }

        return $refund;
    }
}