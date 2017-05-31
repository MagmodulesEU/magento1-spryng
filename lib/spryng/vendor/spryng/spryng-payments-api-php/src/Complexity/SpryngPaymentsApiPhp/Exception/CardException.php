<?php

namespace SpryngPaymentsApiPhp\Exception;

use SpryngPaymentsApiPhp\SpryngPaymentsException;

/**
 * Class CardException
 * @package SpryngPaymentsApiPhp\Exception
 */
class CardException extends SpryngPaymentsException
{
    const CARD_NUMBER_NOT_PROVIDED          = 301;
    const CARD_NUMBER_INVALID_FORMAT        = 302;
    const CVV_NOT_PROVIDED                  = 303;
    const CVV_INVALID_FORMAT                = 304;
    const EXPIRY_MONTH_NOT_PROVIDED         = 305;
    const EXPIRY_MONTH_INVALID_FORMAT       = 306;
    const EXPIRY_YEAR_NOT_PROVIDED          = 307;
    const EXPIRY_YEAR_INVALID_FORMAT        = 308;
}