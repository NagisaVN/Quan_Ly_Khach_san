<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('contracts.create');
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'total_value' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tên hợp đồng.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'total_value.numeric' => 'Giá trị hợp đồng phải là số.',
        ];
    }
}
