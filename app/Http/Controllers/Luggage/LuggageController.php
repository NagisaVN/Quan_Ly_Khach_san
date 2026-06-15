<?php

namespace App\Http\Controllers\Luggage;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Luggage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LuggageController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('luggage.view'), 403);
        $branchId = session('current_branch_id');
        $items = Luggage::query()->with('customer')
            ->where('branch_id', $branchId)
            ->when($request->search, fn ($q, $s) => $q->where('tag_code', 'like', "%{$s}%"))
            ->orderByDesc('id')->paginate(15);

        return view('luggage.index', compact('items'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('luggage.create'), 403);
        $customers = Customer::where('branch_id', session('current_branch_id'))->where('is_active', true)->get();

        return view('luggage.create', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('luggage.create'), 403);
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'storage_location' => 'nullable|string|max:100',
        ]);
        $data['branch_id'] = session('current_branch_id');
        $data['tag_code'] = 'LG-'.strtoupper(Str::random(6));
        $data['status'] = 'stored';
        $data['stored_at'] = now();
        $item = Luggage::create($data);

        return redirect()->route('luggage.show', $item)->with('success', 'Đã lưu hành lý');
    }

    public function show(Luggage $luggage): View
    {
        abort_unless(auth()->user()->can('luggage.view'), 403);
        $luggage->load(['customer', 'booking']);

        return view('luggage.show', compact('luggage'));
    }

    public function edit(Luggage $luggage): View
    {
        abort_unless(auth()->user()->can('luggage.update'), 403);

        return view('luggage.edit', compact('luggage'));
    }

    public function update(Request $request, Luggage $luggage): RedirectResponse
    {
        abort_unless(auth()->user()->can('luggage.update'), 403);
        $data = $request->validate([
            'storage_location' => 'nullable|string|max:100',
            'status' => 'required|in:stored,retrieved',
            'notes' => 'nullable|string',
        ]);
        if ($data['status'] === 'retrieved') {
            $data['retrieved_at'] = now();
        }
        $luggage->update($data);

        return redirect()->route('luggage.show', $luggage)->with('success', 'Cập nhật hành lý');
    }

    public function destroy(Luggage $luggage): RedirectResponse
    {
        abort_unless(auth()->user()->can('luggage.delete'), 403);
        $luggage->delete();

        return redirect()->route('luggage.index')->with('success', 'Đã xóa');
    }
}
