<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLuggageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('luggage.create');
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|integer|exists:customers,id',
            'booking_id' => 'nullable|integer|exists:bookings,id',
            'description' => 'required|string|max:500',
            'quantity' => 'required|integer|min:1',
            'storage_location' => 'required|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Vui lòng chọn khách hàng.',
            'customer_id.exists' => 'Khách hàng không tồn tại.',
            'description.required' => 'Vui lòng nhập mô tả hành lý.',
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
            'storage_location.required' => 'Vui lòng nhập vị trí lưu trữ.',
        ];
    }
}
