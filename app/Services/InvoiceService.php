<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    public function generateFromBooking(Booking $booking): Invoice
    {
        $existing = $booking->invoices()->where('status', '!=', InvoiceStatus::Cancelled->value)->first();
        if ($existing) {
            return $existing->load('items');
        }

        $booking->load(['bookingRooms.room.roomType', 'serviceItems.service']);
        $subtotal = (float) $booking->bookingRooms->sum('total_amount')
            + (float) $booking->serviceItems->sum('total_amount');
        $tax = round($subtotal * 0.1, 2);
        $total = $subtotal + $tax;

        $invoice = Invoice::create([
            'branch_id' => $booking->branch_id,
            'booking_id' => $booking->id,
            'customer_id' => $booking->customer_id,
            'invoice_number' => 'INV-'.strtoupper(Str::random(8)),
            'status' => InvoiceStatus::Issued->value,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'discount_amount' => 0,
            'total_amount' => $total,
            'paid_amount' => 0,
            'balance' => $total,
            'issue_date' => now()->toDateString(),
        ]);

        foreach ($booking->bookingRooms as $br) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_type' => 'room',
                'reference_id' => $br->id,
                'description' => 'Phòng '.$br->room->room_number.' ('.$br->nights.' đêm)',
                'quantity' => $br->nights,
                'unit_price' => $br->rate_snapshot,
                'total_amount' => $br->total_amount,
            ]);
        }

        foreach ($booking->serviceItems as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_type' => 'service',
                'reference_id' => $item->service_id,
                'description' => $item->service->name ?? 'Dịch vụ',
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_amount' => $item->total_amount,
            ]);
        }

        return $invoice->load('items');
    }

    public function recalculateTotals(Invoice $invoice): Invoice
    {
        $invoice->load('items');
        $subtotal = (float) $invoice->items->sum('total_amount');
        $discount = (float) ($invoice->discount_amount ?? 0);
        $taxable = max(0, $subtotal - $discount);
        $tax = round($taxable * 0.1, 2);
        $total = $taxable + $tax;
        $paid = (float) $invoice->paid_amount;
        $balance = max(0, $total - $paid);

        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'balance' => $balance,
            'status' => $balance <= 0 ? InvoiceStatus::Paid->value : ($paid > 0 ? InvoiceStatus::Partial->value : InvoiceStatus::Issued->value),
        ]);

        return $invoice->fresh('items');
    }

    public function applyCoupon(Invoice $invoice, string $couponCode): Invoice
    {
        return DB::transaction(function () use ($invoice, $couponCode) {
            $invoice = Invoice::lockForUpdate()->findOrFail($invoice->id);
            $coupon = Coupon::query()
                ->where('code', $couponCode)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('valid_from')->orWhere('valid_from', '<=', now()->toDateString());
                })
                ->where(function ($q) {
                    $q->whereNull('valid_to')->orWhere('valid_to', '>=', now()->toDateString());
                })
                ->firstOrFail();

            $discount = $coupon->type === 'percent'
                ? round((float) $invoice->subtotal * ((float) $coupon->value / 100), 2)
                : min((float) $coupon->value, (float) $invoice->subtotal);

            if ($coupon->max_discount) {
                $discount = min($discount, (float) $coupon->max_discount);
            }

            $invoice->update(['discount_amount' => $discount, 'coupon_id' => $coupon->id]);
            $coupon->increment('used_count');

            return $this->recalculateTotals($invoice);
        });
    }

    public function processRefund(int $invoiceId, float $amount, string $reason): Refund
    {
        return DB::transaction(function () use ($invoiceId, $amount, $reason) {
            $invoice = Invoice::lockForUpdate()->with('payments')->findOrFail($invoiceId);
            $payment = $invoice->payments()->where('status', 'completed')->latest()->firstOrFail();
            $refundAmount = min($amount, (float) $payment->amount);

            $refund = Refund::create([
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'amount' => $refundAmount,
                'reason' => $reason,
                'status' => 'completed',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $paid = max(0, (float) $invoice->paid_amount - $refundAmount);
            $balance = max(0, (float) $invoice->total_amount - $paid);
            $invoice->update([
                'paid_amount' => $paid,
                'balance' => $balance,
                'status' => $balance <= 0 ? InvoiceStatus::Paid->value : InvoiceStatus::Partial->value,
            ]);

            return $refund;
        });
    }
}
