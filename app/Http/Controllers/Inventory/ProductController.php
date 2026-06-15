<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('inventory.view'), 403);
        $products = Product::query()->where('branch_id', session('current_branch_id'))
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"))
            ->orderBy('name')->paginate(15);

        return view('inventory.products.index', compact('products'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('inventory.create'), 403);

        return view('inventory.products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('inventory.create'), 403);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50',
            'unit' => 'nullable|string|max:20',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
        ]);
        $data['branch_id'] = session('current_branch_id');
        $product = Product::create($data);

        return redirect()->route('inventory.products.show', $product)->with('success', 'Tạo sản phẩm thành công');
    }

    public function show(Product $product): View
    {
        abort_unless(auth()->user()->can('inventory.view'), 403);
        $product->load('stockMovements');

        return view('inventory.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        abort_unless(auth()->user()->can('inventory.update'), 403);

        return view('inventory.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        abort_unless(auth()->user()->can('inventory.update'), 403);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'selling_price' => 'nullable|numeric|min:0',
            'stock_adjustment' => 'nullable|integer',
            'min_stock_level' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($product, $data) {
            if (isset($data['stock_adjustment']) && $data['stock_adjustment'] != 0) {
                $before = $product->stock_quantity;
                $after = $before + (int) $data['stock_adjustment'];
                StockMovement::create([
                    'branch_id' => $product->branch_id,
                    'product_id' => $product->id,
                    'type' => $data['stock_adjustment'] > 0 ? 'in' : 'out',
                    'quantity' => abs($data['stock_adjustment']),
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'notes' => 'Manual adjustment',
                ]);
                $product->stock_quantity = $after;
            }
            unset($data['stock_adjustment']);
            $product->update($data);
        });

        return redirect()->route('inventory.products.show', $product)->with('success', 'Cập nhật kho');
    }

    public function destroy(Product $product): RedirectResponse
    {
        abort_unless(auth()->user()->can('inventory.delete'), 403);
        $product->delete();

        return redirect()->route('inventory.products.index')->with('success', 'Đã xóa');
    }
}
