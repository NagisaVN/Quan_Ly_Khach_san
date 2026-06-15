<?php

namespace App\Http\Controllers\Report;

use App\Exports\RevenueExport;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('reports.view'), 403);

        $branchId = $this->branchId();
        $fromDate = Carbon::parse($request->get('from_date', now()->startOfMonth()->toDateString()));
        $toDate = Carbon::parse($request->get('to_date', now()->toDateString()));
        $groupBy = $request->get('group_by', 'day');

        $revenue = $this->reportService->revenueReport($branchId, $fromDate, $toDate, $groupBy);
        $occupancy = $this->reportService->occupancyReport($branchId, $fromDate, $toDate);
        $topServices = $this->reportService->topServicesReport($branchId, $fromDate, $toDate);
        $topCustomers = $this->reportService->topCustomersReport($branchId, $fromDate, $toDate);

        return view('reports.index', compact(
            'revenue',
            'occupancy',
            'topServices',
            'topCustomers',
            'fromDate',
            'toDate',
            'groupBy'
        ));
    }

    public function exportExcel(Request $request)
    {
        abort_unless($request->user()->can('reports.export'), 403);

        $branchId = $this->branchId();
        $fromDate = Carbon::parse($request->get('from_date', now()->startOfMonth()->toDateString()));
        $toDate = Carbon::parse($request->get('to_date', now()->toDateString()));
        $groupBy = $request->get('group_by', 'day');

        $revenue = $this->reportService->revenueReport($branchId, $fromDate, $toDate, $groupBy);

        return Excel::download(
            new RevenueExport($revenue, $fromDate, $toDate),
            'revenue-report-'.$fromDate->format('Ymd').'-'.$toDate->format('Ymd').'.xlsx'
        );
    }

    public function exportPdf(Request $request): Response
    {
        abort_unless($request->user()->can('reports.export'), 403);

        $branchId = $this->branchId();
        $fromDate = Carbon::parse($request->get('from_date', now()->startOfMonth()->toDateString()));
        $toDate = Carbon::parse($request->get('to_date', now()->toDateString()));
        $groupBy = $request->get('group_by', 'day');

        $revenue = $this->reportService->revenueReport($branchId, $fromDate, $toDate, $groupBy);
        $occupancy = $this->reportService->occupancyReport($branchId, $fromDate, $toDate);

        $pdf = Pdf::loadView('reports.pdf', compact('revenue', 'occupancy', 'fromDate', 'toDate'));

        return $pdf->download('report-'.$fromDate->format('Ymd').'.pdf');
    }

    protected function branchId(): int
    {
        return (int) session('current_branch_id', auth()->user()->current_branch_id);
    }
}
