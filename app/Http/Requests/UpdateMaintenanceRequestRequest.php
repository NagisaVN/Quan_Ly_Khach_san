<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaintenanceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('maintenance.update');
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'priority' => 'sometimes|string|in:low,medium,high,urgent',
            'status' => 'sometimes|string|in:open,in-progress,completed,cancelled',
            'resolution_notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'priority.in' => 'Độ ưu tiên không hợp lệ.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
