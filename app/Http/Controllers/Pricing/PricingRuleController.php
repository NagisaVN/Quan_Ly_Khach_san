<?php

namespace App\Http\Controllers\Pricing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pricing\StorePricingRuleRequest;
use App\Http\Requests\Pricing\StoreSeasonalRateRequest;
use App\Models\PricingRule;
use App\Models\RoomType;
use App\Models\SeasonalRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingRuleController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('pricing.view'), 403);

        $branchId = $this->branchId();
        $rules = PricingRule::query()
            ->forBranch($branchId)
            ->with('roomType')
            ->orderByDesc('priority')
            ->paginate(15);

        $seasonalRates = SeasonalRate::query()
            ->forBranch($branchId)
            ->with('roomType')
            ->orderByDesc('start_date')
            ->paginate(15, ['*'], 'seasonal_page');

        $roomTypes = RoomType::query()->where('is_active', true)->orderBy('name')->get();

        return view('pricing.index', compact('rules', 'seasonalRates', 'roomTypes'));
    }

    public function storeRule(StorePricingRuleRequest $request): RedirectResponse
    {
        PricingRule::create([
            'branch_id' => $this->branchId(),
            'room_type_id' => $request->room_type_id,
            'name' => $request->name,
            'type' => $request->type,
            'conditions' => $request->conditions ?? [],
            'adjustment_type' => $request->adjustment_type,
            'value' => $request->value,
            'priority' => $request->priority ?? 0,
            'valid_from' => $request->valid_from,
            'valid_to' => $request->valid_to,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Đã tạo quy tắc giá');
    }

    public function destroyRule(PricingRule $pricingRule): RedirectResponse
    {
        abort_unless(auth()->user()->can('pricing.delete'), 403);
        abort_unless((int) $pricingRule->branch_id === $this->branchId(), 403);

        $pricingRule->delete();

        return back()->with('success', 'Đã xóa quy tắc giá');
    }

    public function storeSeasonal(StoreSeasonalRateRequest $request): RedirectResponse
    {
        SeasonalRate::create([
            'branch_id' => $this->branchId(),
            'room_type_id' => $request->room_type_id,
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'rate' => $request->rate,
            'adjustment_percent' => $request->adjustment_percent,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Đã tạo giá theo mùa');
    }

    public function destroySeasonal(SeasonalRate $seasonalRate): RedirectResponse
    {
        abort_unless(auth()->user()->can('pricing.delete'), 403);
        abort_unless((int) $seasonalRate->branch_id === $this->branchId(), 403);

        $seasonalRate->delete();

        return back()->with('success', 'Đã xóa giá theo mùa');
    }

    protected function branchId(): int
    {
        return (int) session('current_branch_id', auth()->user()->current_branch_id);
    }
}
