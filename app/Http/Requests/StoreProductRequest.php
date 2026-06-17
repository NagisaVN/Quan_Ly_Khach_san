<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('inventory.create');
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku',
            'unit' => 'required|string|max:50',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'sku.required' => 'Vui lòng nhập mã SKU.',
            'sku.unique' => 'Mã SKU đã tồn tại.',
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp.',
            'cost_price.required' => 'Vui lòng nhập giá vốn.',
            'selling_price.required' => 'Vui lòng nhập giá bán.',
            'stock_quantity.required' => 'Vui lòng nhập số lượng tồn kho.',
            'min_stock_level.required' => 'Vui lòng nhập mức tồn kho tối thiểu.',
        ];
    }
}
