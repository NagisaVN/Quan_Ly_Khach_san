<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class ChangeRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('bookings.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'old_room_id' => ['required', 'integer', 'exists:rooms,id'],
            'new_room_id' => ['required', 'integer', 'exists:rooms,id', 'different:old_room_id'],
        ];
    }
}
