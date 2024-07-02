<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedAccess extends Exception
{
    public function __construct($message = 'Unauthorized access', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
