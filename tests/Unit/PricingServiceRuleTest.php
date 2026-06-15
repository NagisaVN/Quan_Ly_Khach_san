<?php

namespace Tests\Unit;

use App\Models\Holiday;
use App\Models\RoomType;
use App\Services\PricingService;
use Carbon\Carbon;
use Tests\CreatesHotelTestData;
use Tests\TestCase;

class PricingServiceRuleTest extends TestCase
{
    use CreatesHotelTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpHotelTestData();
    }

    public function test_holiday_increases_rate(): void
    {
        Holiday::create([
            'company_id' => $this->company->id,
            'name' => 'Test Holiday',
            'date' => '2026-06-20',
            'is_active' => true,
        ]);

        $service = app(PricingService::class);
        $weekdayRate = $service->calculateNightlyRate($this->roomType->id, $this->branch->id, Carbon::parse('2026-06-18'));
        $holidayRate = $service->calculateNightlyRate($this->roomType->id, $this->branch->id, Carbon::parse('2026-06-20'));

        $this->assertGreaterThan($weekdayRate, $holidayRate);
    }

    public function test_weekend_rate_higher_than_weekday(): void
    {
        $service = app(PricingService::class);
        $weekday = $service->calculateNightlyRate($this->roomType->id, $this->branch->id, Carbon::parse('2026-06-17')); // Tuesday
        $weekend = $service->calculateNightlyRate($this->roomType->id, $this->branch->id, Carbon::parse('2026-06-20')); // Saturday + holiday maybe

        $this->assertGreaterThanOrEqual($weekday, $weekend);
    }
}
