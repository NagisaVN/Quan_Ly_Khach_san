<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index(): View
    {
        $branchId = session('current_branch_id');

        return view('dashboard.index', [
            'kpis' => $this->dashboardService->getKpis($branchId),
            'revenueChart' => $this->dashboardService->getRevenueChart($branchId),
            'occupancyChart' => $this->dashboardService->getOccupancyChart($branchId),
            'bookingStatusChart' => $this->dashboardService->getBookingStatusChart($branchId),
            'recentBookings' => $this->dashboardService->getRecentBookings($branchId),
        ]);
    }
}
