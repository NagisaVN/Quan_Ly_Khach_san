<?php

namespace Tests\Feature\Booking;

use App\DTOs\CreateBookingDto;
use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\RoomStatus;
use App\Models\Room;
use App\Services\BookingService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Tests\CreatesHotelTestData;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use CreatesHotelTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpHotelTestData();
    }

    public function test_full_booking_flow_create_checkin_payment_checkout(): void
    {
        Carbon::setTestNow('2026-06-15 10:00:00');

        $this->actingAs($this->receptionist);

        $bookingService = app(BookingService::class);
        $paymentService = app(PaymentService::class);

        $checkIn = Carbon::parse('2026-06-15');
        $checkOut = Carbon::parse('2026-06-17');

        $booking = $bookingService->createBooking(new CreateBookingDto(
            customerId: $this->customer->id,
            branchId: $this->branch->id,
            checkInDate: $checkIn,
            checkOutDate: $checkOut,
            roomIds: [$this->room->id],
            adults: 2,
            createdBy: $this->receptionist->id,
        ));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => BookingStatus::Confirmed->value,
            'customer_id' => $this->customer->id,
        ]);

        $this->assertDatabaseHas('booking_rooms', [
            'booking_id' => $booking->id,
            'room_id' => $this->room->id,
        ]);

        $this->room->refresh();
        $this->assertEquals(RoomStatus::Reserved, $this->room->status);

        $booking = $bookingService->checkIn($booking->id);

        $this->assertEquals(BookingStatus::CheckedIn, $booking->status);
        $this->assertNotNull($booking->actual_check_in_at);
        $this->room->refresh();
        $this->assertEquals(RoomStatus::Occupied, $this->room->status);

        $invoice = $booking->invoices->first();
        $this->assertNotNull($invoice);
        $this->assertGreaterThan(0, (float) $invoice->total_amount);

        $paymentService->processPayment(
            $invoice->id,
            (float) $invoice->balance,
            PaymentMethod::Cash
        );

        $invoice->refresh();
        $this->assertEquals(0, (float) $invoice->balance);
        $this->assertEquals('paid', $invoice->status->value);

        $booking = $bookingService->checkOut($booking->id);

        $this->assertEquals(BookingStatus::CheckedOut, $booking->status);
        $this->room->refresh();
        $this->assertEquals(RoomStatus::Cleaning, $this->room->status);

        $this->customer->refresh();
        $this->assertGreaterThan(0, $this->customer->loyalty_points);
    }

    public function test_booking_index_page_is_accessible(): void
    {
        $this->actingAs($this->receptionist)
            ->withSession(['current_branch_id' => $this->branch->id])
            ->get(route('bookings.index'))
            ->assertOk()
            ->assertSee('Quản lý đặt phòng');
    }
}
