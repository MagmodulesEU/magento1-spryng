<?php

namespace SpryngPaymentsApiPhp\Helpers;

use SpryngPaymentsApiPhp\Exception\RequestException;
use SpryngPaymentsApiPhp\Object\Good;
use SpryngPaymentsApiPhp\Exception\TransactionException;
use SpryngPaymentsApiPhp\Object\GoodsList;

class KlarnaHelper
{
    public static function validateKlarnaInitializeArguments(array $arguments)
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

        if (!isset($arguments['details']['pclass']) || empty($arguments['details']['pclass']))
        {
            throw new TransactionException("Cannot initiate a Klarna transaction without a PClass", 210);
        }

        if (!isset($arguments['details']['goods_list']) || empty($arguments['details']['goods_list']) ||
            count($arguments['details']['goods_list']) < 1)
        {
            throw new TransactionException("Cannot initiate Klarna transaction with at least one product in the goods_list", 210);
        }
    }

    public static function validateCaptureArguments($arguments)
    {
        foreach ($arguments['details']['flags'] as $flag)
        {
            if ($flag !== 4 && $flag !== 8)
            {
                throw new RequestException('Klarna capture flag(s) invalid.', 101);
            }
        }
    }

    public static function parseGoodsList($goodsList)
    {
        if ($goodsList instanceof GoodsList)
        {
            $goodsList = $goodsList->toArray();
        }
        else
        {
            foreach($goodsList as $good)
            {
                if ($good instanceof Good)
                {
                    $good = $good->toJson();
                }
            }
        }

        return $goodsList;
    }
}