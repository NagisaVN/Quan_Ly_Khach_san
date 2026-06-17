<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query()->with('supplier');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('sku', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function findOrFail(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function create(array $data): Product
    {
        $data['is_active'] = true;

        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function getLowStockProducts(int $branchId): array
    {
        return Product::query()
            ->where('branch_id', $branchId)
            ->whereRaw('stock_quantity <= min_stock_level')
            ->where('is_active', true)
            ->orderBy('stock_quantity')
            ->get()
            ->toArray();
    }
}
