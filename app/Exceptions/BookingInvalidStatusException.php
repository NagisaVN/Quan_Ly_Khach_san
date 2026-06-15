<?php

namespace App\Exceptions;

use Exception;

class BookingInvalidStatusException extends Exception
{
    public function __construct(string $message = 'Trạng thái booking không hợp lệ cho thao tác này')
    {
        parent::__construct($message);
    }
}
