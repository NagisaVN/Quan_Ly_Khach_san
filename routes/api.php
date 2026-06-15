<?php

use App\Http\Controllers\Api\BookingApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('bookings/availability', [BookingApiController::class, 'availability'])
        ->name('api.bookings.availability');
});
