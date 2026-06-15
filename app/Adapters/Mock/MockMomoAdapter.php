<?php

namespace App\Adapters\Mock;

use App\Adapters\Contracts\PaymentGatewayInterface;
use App\Models\Payment;

class MockMomoAdapter implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'momo';
    }

    public function createPaymentUrl(Payment $payment, array $options = []): string
    {
        $orderId = 'MOMO'.str_pad((string) $payment->id, 8, '0', STR_PAD_LEFT);

        return route('payments.callback', [
            'gateway' => 'momo',
            'payment_id' => $payment->id,
            'orderId' => $orderId,
            'amount' => $payment->amount,
            'resultCode' => 0,
        ]);
    }

    public function verifyCallback(array $data): bool
    {
        return (int) ($data['resultCode'] ?? -1) === 0
            && isset($data['payment_id'], $data['orderId']);
    }

    public function getTransactionId(array $data): ?string
    {
        return $data['orderId'] ?? null;
    }
}
