<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\StoreCustomerRequest;
use App\Http\Requests\Customers\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(private CustomerService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Customer::class);

        $customers = $this->service->paginate($request->only('search'));

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        $this->authorize('create', Customer::class);

        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $this->authorize('create', Customer::class);

        $customer = $this->service->create(
            $request->safe()->except('cccd_image'),
            $request->file('cccd_image')
        );

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Tạo khách hàng thành công.');
    }

    public function show(Customer $customer): View
    {
        $this->authorize('view', $customer);

        $customer->load('documents', 'loyaltyTier');

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        $this->authorize('update', $customer);

        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->authorize('update', $customer);

        $this->service->update(
            $customer,
            $request->safe()->except('cccd_image'),
            $request->file('cccd_image')
        );

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Cập nhật khách hàng thành công.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->authorize('delete', $customer);

        $this->service->delete($customer);

        return redirect()->route('customers.index')
            ->with('success', 'Xóa khách hàng thành công.');
    }
}
