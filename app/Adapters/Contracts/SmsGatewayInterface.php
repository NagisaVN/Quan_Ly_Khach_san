<?php

namespace App\Adapters\Contracts;

interface SmsGatewayInterface
{
    public function sendSms(string $phone, string $message, array $options = []): array;
}
