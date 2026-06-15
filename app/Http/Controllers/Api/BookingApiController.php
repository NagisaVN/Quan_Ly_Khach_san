<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Booking\BookingController;
use App\Http\Requests\Booking\CheckAvailabilityRequest;
use Illuminate\Http\JsonResponse;

class BookingApiController extends BookingController
{
    public function availability(CheckAvailabilityRequest $request): JsonResponse
    {
        return parent::availability($request);
    }
}
