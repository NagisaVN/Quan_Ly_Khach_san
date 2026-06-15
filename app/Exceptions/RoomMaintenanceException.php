<?php
namespace App\Exceptions;
class RoomMaintenanceException extends \Exception {
    public function __construct(string $message = "Không thể check-in: phòng đang bảo trì.") { parent::__construct($message); }
}