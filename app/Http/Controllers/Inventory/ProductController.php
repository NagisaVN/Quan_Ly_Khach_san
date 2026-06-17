<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private ProductService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $products = $this->service->paginate($request->only('search'));

        return view('inventory.products.index', compact('products'));
    }

    public function create(): View
    {
        $this->authorize('create', Product::class);
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('inventory.products.create', compact('suppliers'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['branch_id'] = session('current_branch_id');
        $product = $this->service->create($data);

        return redirect()->route('inventory.products.show', $product)
            ->with('success', 'Tạo sản phẩm thành công.');
    }

    public function show(Product $product): View
    {
        $this->authorize('view', $product);
        $product->load(['stockMovements', 'supplier']);

        return view('inventory.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('inventory.products.edit', compact('product', 'suppliers'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        
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
            $this->service->update($product, $data);
        });

        return redirect()->route('inventory.products.show', $product)
            ->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);
        $this->service->delete($product);

        return redirect()->route('inventory.products.index')
            ->with('success', 'Xóa sản phẩm thành công.');
    }
}
