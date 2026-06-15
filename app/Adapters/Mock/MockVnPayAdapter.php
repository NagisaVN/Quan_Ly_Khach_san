<?php

namespace App\Adapters\Mock;

use App\Adapters\Contracts\PaymentGatewayInterface;
use App\Models\Payment;

class MockVnPayAdapter implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'vnpay';
    }

    public function createPaymentUrl(Payment $payment, array $options = []): string
    {
        $txnRef = 'VNP'.str_pad((string) $payment->id, 8, '0', STR_PAD_LEFT);

        return route('payments.callback', [
            'gateway' => 'vnpay',
            'payment_id' => $payment->id,
            'txn_ref' => $txnRef,
            'amount' => $payment->amount,
            'status' => 'success',
        ]);
    }

    public function verifyCallback(array $data): bool
    {
        return ($data['status'] ?? '') === 'success'
            && isset($data['payment_id'], $data['txn_ref']);
    }

    public function getTransactionId(array $data): ?string
    {
        return $data['txn_ref'] ?? null;
    }
}
