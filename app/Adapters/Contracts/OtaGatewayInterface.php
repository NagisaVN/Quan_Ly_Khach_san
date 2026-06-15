<?php

namespace App\Adapters\Contracts;

interface OtaGatewayInterface
{
    public function syncBooking(array $bookingData): array;

    public function fetchReservations(array $filters = []): array;
}
