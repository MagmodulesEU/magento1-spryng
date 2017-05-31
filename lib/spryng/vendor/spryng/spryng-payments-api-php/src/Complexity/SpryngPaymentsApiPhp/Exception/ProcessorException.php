<?php

namespace SpryngPaymentsApiPhp\Exception;

use SpryngPaymentsApiPhp\SpryngPaymentsException;

class ProcessorException extends SpryngPaymentsException
{
    const INVALID_CONFIGURATION_PARAMETERS = 401;
}