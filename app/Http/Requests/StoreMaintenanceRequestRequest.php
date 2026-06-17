<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('maintenance.create');
    }

    public function rules(): array
    {
        return [
            'room_id' => 'required|integer|exists:rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.required' => 'Vui lòng chọn phòng.',
            'room_id.exists' => 'Phòng không tồn tại.',
            'title.required' => 'Vui lòng nhập tiêu đề yêu cầu.',
            'description.required' => 'Vui lòng nhập mô tả chi tiết.',
            'priority.required' => 'Vui lòng chọn độ ưu tiên.',
            'priority.in' => 'Độ ưu tiên không hợp lệ.',
        ];
    }
}
