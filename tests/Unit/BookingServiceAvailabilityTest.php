<?php

namespace Tests\Unit;

use App\DTOs\CreateBookingDto;
use App\Enums\RoomStatus;
use App\Exceptions\RoomNotAvailableException;
use App\Models\Room;
use App\Services\BookingService;
use Carbon\Carbon;
use Tests\CreatesHotelTestData;
use Tests\TestCase;

class BookingServiceAvailabilityTest extends TestCase
{
    use CreatesHotelTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpHotelTestData();
    }

    public function test_check_availability_returns_available_room(): void
    {
        Carbon::setTestNow('2026-06-15');

        $service = app(BookingService::class);

        $result = $service->checkAvailability(
            $this->branch->id,
            Carbon::parse('2026-06-15'),
            Carbon::parse('2026-06-17'),
            null,
            $this->roomType->id
        );

        $this->assertEquals(1, $result['total']);
        $this->assertTrue($result['available_rooms']->contains('id', $this->room->id));
    }

    public function test_maintenance_room_is_not_available(): void
    {
        Carbon::setTestNow('2026-06-15');

        $this->room->update(['status' => RoomStatus::Maintenance]);

        $service = app(BookingService::class);

        $result = $service->checkAvailability(
            $this->branch->id,
            Carbon::parse('2026-06-15'),
            Carbon::parse('2026-06-17')
        );

        $this->assertEquals(0, $result['total']);
    }

    public function test_overlapping_booking_blocks_availability(): void
    {
        Carbon::setTestNow('2026-06-15');

        $service = app(BookingService::class);

        $service->createBooking(new CreateBookingDto(
            customerId: $this->customer->id,
            branchId: $this->branch->id,
            checkInDate: Carbon::parse('2026-06-15'),
            checkOutDate: Carbon::parse('2026-06-17'),
            roomIds: [$this->room->id],
            createdBy: $this->receptionist->id,
        ));

        $result = $service->checkAvailability(
            $this->branch->id,
            Carbon::parse('2026-06-16'),
            Carbon::parse('2026-06-18'),
            [$this->room->id]
        );

        $this->assertEquals(0, $result['total']);
    }

    public function test_create_booking_throws_when_room_not_available(): void
    {
        Carbon::setTestNow('2026-06-15');

        $service = app(BookingService::class);

        $service->createBooking(new CreateBookingDto(
            customerId: $this->customer->id,
            branchId: $this->branch->id,
            checkInDate: Carbon::parse('2026-06-15'),
            checkOutDate: Carbon::parse('2026-06-17'),
            roomIds: [$this->room->id],
            createdBy: $this->receptionist->id,
        ));

        $this->expectException(RoomNotAvailableException::class);

        $service->createBooking(new CreateBookingDto(
            customerId: $this->customer->id,
            branchId: $this->branch->id,
            checkInDate: Carbon::parse('2026-06-16'),
            checkOutDate: Carbon::parse('2026-06-18'),
            roomIds: [$this->room->id],
            createdBy: $this->receptionist->id,
        ));
    }

    public function test_weekend_pricing_increases_rate(): void
    {
        Carbon::setTestNow('2026-06-14'); // Saturday

        $pricing = app(\App\Services\PricingService::class);
        $result = $pricing->calculateNightlyRate(
            $this->roomType->id,
            $this->branch->id,
            Carbon::parse('2026-06-14'),
            $this->customer->id
        );

        $this->assertGreaterThan($result['base_rate'], $result['final_rate']);
    }
}
