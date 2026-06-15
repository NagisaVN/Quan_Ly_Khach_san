<?php

namespace App\Http\Controllers\Payment;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\ProcessPaymentRequest;
use App\Models\Invoice;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
    ) {}

    public function create(Invoice $invoice): View
    {
        abort_unless(auth()->user()->can('payments.create'), 403);

        $invoice->load(['customer', 'booking']);

        return view('payments.create', compact('invoice'));
    }

    public function store(ProcessPaymentRequest $request, Invoice $invoice): RedirectResponse
    {
        $method = PaymentMethod::from($request->payment_method);
        $amount = (float) $request->amount;

        if (in_array($method, [PaymentMethod::Momo, PaymentMethod::Vnpay], true)) {
            $result = $this->paymentService->createGatewayPayment($invoice->id, $amount, $method);

            return redirect()->away($result['redirect_url']);
        }

        $this->paymentService->processPayment(
            $invoice->id,
            $amount,
            $method,
            $request->reference,
            $request->notes
        );

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Thanh toán thành công');
    }

    public function callback(Request $request): RedirectResponse
    {
        $paymentId = (int) $request->get('payment_id');

        try {
            $payment = $this->paymentService->confirmGatewayPayment($paymentId, $request->all());

            return redirect()
                ->route('invoices.show', $payment->invoice_id)
                ->with('success', 'Thanh toán qua cổng thành công');
        } catch (\Throwable $e) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Thanh toán thất bại: '.$e->getMessage());
        }
    }
}
