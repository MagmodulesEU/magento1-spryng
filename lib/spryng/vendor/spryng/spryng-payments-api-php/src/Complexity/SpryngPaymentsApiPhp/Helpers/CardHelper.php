<?php

namespace SpryngPaymentsApiPhp\Helpers;
use SpryngPaymentsApiPhp\Exception\CardException;
use SpryngPaymentsApiPhp\Object\Card;

/**
 * Class CardHelper
 * @package SpryngPaymentsApiPhp\Helpers
 */
class CardHelper
{
    public static function fillCard($responseObj)
    {
        $obj = new Card();

        $obj->_id           = (isset($responseObj->_id)) ? $responseObj->_id : null;
        $obj->bin           = (isset($responseObj->bin)) ? $responseObj->bin : null;
        $obj->last_four     = (isset($responseObj->last_four)) ? $responseObj->last_four : null;
        $obj->brand         = (isset($responseObj->brand)) ? $responseObj->brand : null;
        $obj->expiry_year   = (isset($responseObj->expiry_year)) ? $responseObj->expiry_year : null;
        $obj->issuer_name   = (isset($responseObj->issuer_name)) ? $responseObj->issuer_name : null;
        $obj->expiry_month  = (isset($responseObj->expiry_month)) ? $responseObj->expiry_month : null;
        $obj->type          = (isset($responseObj->type)) ? $responseObj->type : null;
        $obj->cvv_verified  = (isset($responseObj->cvv_verified)) ? $responseObj->cvv_verified : null;

        return $obj;
    }

    /**
     * @param $arguments
     * @throws CardException
     */
    public static function validateNewCardArguments($arguments)
    {
        if (!isset($arguments['card_number']))
        {
            throw new CardException("No card number provided.", 301);
        }

        if (strpos('-', $arguments['card_number']) !== false)
        {
            throw new CardException("Card number is not formatted correctly.", 302);
        }

        if (! ctype_digit( str_replace(' ', '', $arguments['card_number'] ) ) )
        {
            throw new CardException("Card number is not formatted correctly.", 302);
        }

        if (strlen( (string) str_replace(' ', '', $arguments['card_number'] ) ) != 16 )
        {
            throw new CardException("Card number is not formatted correctly.", 302);
        }

        if (!isset($arguments['cvv']))
        {
            throw new CardException("CVV is not provided.", 303);
        }

        if (strlen($arguments['cvv']) !== 3)
        {
            throw new CardException("CVV is not formatted correctly.", 304);
        }

        if (!ctype_digit($arguments['cvv']))
        {
            throw new CardException("CVV is not formatted correctly.", 304);
        }

        if (!isset($arguments['expiry_month']))
        {
            throw new CardException("Expiry month is not provided.", 305);
        }

        if (strlen($arguments['expiry_month']) !== 2)
        {
            throw new CardException("Expiry month is not formatted correctly.", 306);
        }

        if (!ctype_digit($arguments['expiry_month']))
        {
            throw new CardException("Expiry month is not formatted correctly.", 306);
        }

        if ((int) $arguments['expiry_month'] > 12 || (int) $arguments['expiry_month'] < 1)
        {
            throw new CardException("Expiry month is not formatted correctly.", 306);
        }

        if (!isset($arguments['expiry_year']))
        {
            throw new CardException("Expiry year is not provided.", 307);
        }

        if (strlen($arguments['expiry_year']) !== 2)
        {
            throw new CardException("Expiry year is not formatted correctly.", 308);
        }

        if (!ctype_digit($arguments['expiry_year']))
        {
            throw new CardException("Expiry year is not formatted correctly.", 308);
        }
    }
}