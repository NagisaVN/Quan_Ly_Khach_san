<?php
namespace App\Exceptions;
class PaymentRequiredException extends \Exception {
    public function __construct(string $message = "Vui lòng thanh toán đủ trước khi check-out.") { parent::__construct($message); }
}