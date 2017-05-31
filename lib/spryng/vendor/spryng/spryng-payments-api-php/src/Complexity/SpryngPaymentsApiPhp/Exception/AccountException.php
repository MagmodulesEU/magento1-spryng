<?php

namespace SpryngPaymentsApiPhp\Exception;

use SpryngPaymentsApiPhp\SpryngPaymentsException;

/**
 * Class AccountException
 * @package SpryngPaymentsApiPhp\Exception
 */
class AccountException extends SpryngPaymentsException
{
    const ACCOUNT_NOT_FOUND     = 601;
}