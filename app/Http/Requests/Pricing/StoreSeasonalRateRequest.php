<?php

namespace App\Http\Requests\Pricing;

use Illuminate\Foundation\Http\FormRequest;

class StoreSeasonalRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('pricing.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'rate' => ['nullable', 'numeric', 'min:0'],
            'adjustment_percent' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
