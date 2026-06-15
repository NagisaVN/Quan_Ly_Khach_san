<?php

namespace App\Adapters\Contracts;

use App\Models\Payment;

interface PaymentGatewayInterface
{
    public function getName(): string;

    public function createPaymentUrl(Payment $payment, array $options = []): string;

    public function verifyCallback(array $data): bool;

    public function getTransactionId(array $data): ?string;
}
