<?php

namespace App\Http\Controllers\Luggage;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLuggageRequest;
use App\Http\Requests\UpdateLuggageRequest;
use App\Models\Customer;
use App\Models\Luggage;
use App\Services\LuggageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LuggageController extends Controller
{
    public function __construct(private LuggageService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Luggage::class);
        $branchId = session('current_branch_id');
        $items = $this->service->paginate([
            'search' => $request->search,
            'branch_id' => $branchId,
        ]);

        return view('luggage.index', compact('items'));
    }

    public function create(): View
    {
        $this->authorize('create', Luggage::class);
        $customers = Customer::where('branch_id', session('current_branch_id'))
            ->where('is_active', true)
            ->get();

        return view('luggage.create', compact('customers'));
    }

    public function store(StoreLuggageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['branch_id'] = session('current_branch_id');
        $item = $this->service->create($data);

        return redirect()->route('luggage.show', $item)
            ->with('success', 'Lưu hành lý thành công.');
    }

    public function show(Luggage $luggage): View
    {
        $this->authorize('view', $luggage);
        $luggage->load(['customer', 'booking']);

        return view('luggage.show', compact('luggage'));
    }

    public function edit(Luggage $luggage): View
    {
        $this->authorize('update', $luggage);

        return view('luggage.edit', compact('luggage'));
    }

    public function update(UpdateLuggageRequest $request, Luggage $luggage): RedirectResponse
    {
        $this->authorize('update', $luggage);
        $this->service->update($luggage, $request->validated());

        return redirect()->route('luggage.show', $luggage)
            ->with('success', 'Cập nhật hành lý thành công.');
    }

    public function destroy(Luggage $luggage): RedirectResponse
    {
        $this->authorize('delete', $luggage);
        $this->service->delete($luggage);

        return redirect()->route('luggage.index')
            ->with('success', 'Xóa hành lý thành công.');
    }
}
