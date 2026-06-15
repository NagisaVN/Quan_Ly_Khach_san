<?php
namespace App\Exceptions;
class RoomNotAvailableException extends \Exception {
    public function __construct(string $message = "Phòng đã được đặt trong khoảng thời gian này.") { parent::__construct($message); }
}