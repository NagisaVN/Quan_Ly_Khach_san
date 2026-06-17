<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('inventory.update');
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'name' => 'sometimes|string|max:255',
            'sku' => 'sometimes|string|max:50|unique:products,sku,'.$productId,
            'unit' => 'sometimes|string|max:50',
            'cost_price' => 'sometimes|numeric|min:0',
            'selling_price' => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'min_stock_level' => 'sometimes|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
