<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Bank = 'bank';
    case Momo = 'momo';
    case Vnpay = 'vnpay';
    case Qr = 'qr';
}
