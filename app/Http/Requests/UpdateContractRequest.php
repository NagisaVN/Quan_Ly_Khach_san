<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('contracts.update');
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'status' => 'sometimes|string|in:active,expired,cancelled',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'total_value' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Trạng thái hợp đồng không hợp lệ.',
        ];
    }
}
