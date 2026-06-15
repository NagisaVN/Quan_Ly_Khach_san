<?php

namespace Tests\Feature\Payment;

use App\Enums\PaymentMethod;
use App\Exceptions\PaymentRequiredException;
use App\Services\BookingService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Tests\CreatesHotelTestData;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use CreatesHotelTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpHotelTestData();
        Carbon::setTestNow('2026-06-15 10:00:00');
    }

    public function test_checkout_blocked_when_invoice_unpaid(): void
    {
        $this->actingAs($this->receptionist);

        $bookingService = app(BookingService::class);
        $booking = $bookingService->createBooking([
            'customer_id' => $this->customer->id,
            'branch_id' => $this->branch->id,
            'check_in_date' => '2026-06-15',
            'check_out_date' => '2026-06-17',
            'room_ids' => [$this->room->id],
            'adults' => 2,
        ]);

        $bookingService->checkIn($booking->id);

        $this->expectException(PaymentRequiredException::class);
        $bookingService->checkOut($booking->id);
    }

    public function test_payment_clears_invoice_balance(): void
    {
        $this->actingAs($this->receptionist);

        $bookingService = app(BookingService::class);
        $paymentService = app(PaymentService::class);

        $booking = $bookingService->createBooking([
            'customer_id' => $this->customer->id,
            'branch_id' => $this->branch->id,
            'check_in_date' => '2026-06-15',
            'check_out_date' => '2026-06-17',
            'room_ids' => [$this->room->id],
            'adults' => 2,
        ]);

        $booking = $bookingService->checkIn($booking->id);
        $invoice = $booking->invoices->first();

        $paymentService->processPayment($invoice, (float) $invoice->balance, PaymentMethod::Cash);

        $invoice->refresh();
        $this->assertEquals(0, (float) $invoice->balance);
    }
}
