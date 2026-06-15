<?php

namespace App\Services;

use App\Adapters\Contracts\PaymentGatewayInterface;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Models\IntegrationLog;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {}

    public function processPayment(
        Invoice|int $invoice,
        float $amount,
        PaymentMethod|string $method,
        ?string $reference = null,
        ?string $notes = null,
    ): Payment {
        $invoice = $invoice instanceof Invoice ? $invoice : Invoice::findOrFail($invoice);
        $methodValue = $method instanceof PaymentMethod ? $method->value : $method;

        return DB::transaction(function () use ($invoice, $amount, $methodValue, $reference, $notes) {
            $invoice = Invoice::lockForUpdate()->findOrFail($invoice->id);
            $payAmount = min($amount, (float) $invoice->balance);

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'branch_id' => $invoice->branch_id,
                'payment_number' => 'PAY-'.strtoupper(Str::random(8)),
                'amount' => $payAmount,
                'payment_method' => $methodValue,
                'status' => 'completed',
                'reference' => $reference,
                'notes' => $notes,
                'paid_at' => now(),
            ]);

            $this->updateInvoiceBalance($invoice, $payAmount);

            Receipt::create([
                'payment_id' => $payment->id,
                'receipt_number' => 'RCP-'.strtoupper(Str::random(8)),
                'amount' => $payAmount,
                'issued_at' => now(),
            ]);

            return $payment;
        });
    }

    public function createGatewayPayment(int $invoiceId, float $amount, PaymentMethod $method): array
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $adapter = $this->resolveGateway($method);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'branch_id' => $invoice->branch_id,
            'payment_number' => 'PAY-'.strtoupper(Str::random(8)),
            'amount' => min($amount, (float) $invoice->balance),
            'payment_method' => $method->value,
            'status' => 'pending',
            'paid_at' => null,
        ]);

        $redirectUrl = $adapter->createPaymentUrl($payment);

        IntegrationLog::create([
            'branch_id' => $invoice->branch_id,
            'provider' => $adapter->getName(),
            'action' => 'create_payment',
            'request_payload' => ['payment_id' => $payment->id, 'amount' => $payment->amount],
            'response_payload' => ['redirect_url' => $redirectUrl],
            'status' => 'success',
        ]);

        return [
            'payment_id' => $payment->id,
            'redirect_url' => $redirectUrl,
        ];
    }

    public function confirmGatewayPayment(int $paymentId, array $callbackData): Payment
    {
        return DB::transaction(function () use ($paymentId, $callbackData) {
            $payment = Payment::lockForUpdate()->findOrFail($paymentId);
            $method = PaymentMethod::from($payment->payment_method);
            $adapter = $this->resolveGateway($method);

            if (! $adapter->verifyCallback($callbackData)) {
                IntegrationLog::create([
                    'branch_id' => $payment->branch_id,
                    'provider' => $adapter->getName(),
                    'action' => 'callback_verify',
                    'request_payload' => $callbackData,
                    'status' => 'failed',
                ]);

                throw new \RuntimeException('Xác thực thanh toán cổng thất bại.');
            }

            $payment->update([
                'status' => 'completed',
                'reference' => $adapter->getTransactionId($callbackData),
                'paid_at' => now(),
            ]);

            $invoice = Invoice::lockForUpdate()->findOrFail($payment->invoice_id);
            $this->updateInvoiceBalance($invoice, (float) $payment->amount);

            Receipt::create([
                'payment_id' => $payment->id,
                'receipt_number' => 'RCP-'.strtoupper(Str::random(8)),
                'amount' => $payment->amount,
                'issued_at' => now(),
            ]);

            IntegrationLog::create([
                'branch_id' => $payment->branch_id,
                'provider' => $adapter->getName(),
                'action' => 'callback_confirm',
                'request_payload' => $callbackData,
                'status' => 'success',
            ]);

            return $payment->fresh(['invoice']);
        });
    }

    private function updateInvoiceBalance(Invoice $invoice, float $payAmount): void
    {
        $paid = (float) $invoice->paid_amount + $payAmount;
        $balance = max(0, (float) $invoice->total_amount - $paid);
        $invoice->update([
            'paid_amount' => $paid,
            'balance' => $balance,
            'status' => $balance <= 0 ? InvoiceStatus::Paid->value : InvoiceStatus::Partial->value,
        ]);
    }

    private function resolveGateway(PaymentMethod $method): PaymentGatewayInterface
    {
        return match ($method) {
            PaymentMethod::Momo => app(\App\Adapters\Mock\MockMomoAdapter::class),
            PaymentMethod::Vnpay => app(\App\Adapters\Mock\MockVnPayAdapter::class),
            default => app(\App\Adapters\Mock\MockVnPayAdapter::class),
        };
    }
}
