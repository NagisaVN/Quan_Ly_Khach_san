<?php

namespace App\DTOs;

use Carbon\Carbon;

readonly class CreateBookingDto
{
    public function __construct(
        public int $customerId,
        public int $branchId,
        public Carbon $checkInDate,
        public Carbon $checkOutDate,
        public array $roomIds,
        public int $adults = 1,
        public int $children = 0,
        public string $source = 'offline',
        public ?string $specialRequests = null,
        public ?int $createdBy = null,
    ) {}
}
