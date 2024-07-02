<?php

namespace App\Exceptions;

use Exception;

class UnexpectedField extends Exception
{
    protected $unexpectedFields;

    public function __construct(array $unexpectedFields)
    {
        $this->unexpectedFields = $unexpectedFields;
        parent::__construct("Field yang tidak diharapkan: " . implode(', ', $unexpectedFields));
    }

    public function getUnexpectedFields(){
        return $this->unexpectedFields;
    }
}
