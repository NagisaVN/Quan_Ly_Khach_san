<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLuggageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('luggage.update');
    }

    public function rules(): array
    {
        return [
            'description' => 'sometimes|string|max:500',
            'quantity' => 'sometimes|integer|min:1',
            'storage_location' => 'sometimes|string|max:100',
            'status' => 'sometimes|string|in:stored,retrieved,lost,damaged',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
