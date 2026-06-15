<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class ExtendBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('bookings.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'new_check_out_date' => ['required', 'date', 'after:check_out_date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $booking = $this->route('booking');

        if ($booking) {
            $this->merge([
                'check_out_date' => $booking->check_out_date?->toDateString(),
            ]);
        }
    }
}
