<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Invoice::class);

        $branchId = (int) session('current_branch_id', auth()->user()->current_branch_id);
        $invoices = Invoice::query()
            ->with(['customer', 'booking'])
            ->where('branch_id', $branchId)
            ->when($request->get('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->get('search'), function ($q, $search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($cq) => $cq->where('phone', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice): View
    {
        abort_unless(auth()->user()->can('payments.view'), 403);

        $invoice->load(['items', 'customer', 'booking', 'payments']);

        return view('invoices.show', compact('invoice'));
    }

    public function pdf(Invoice $invoice): Response
    {
        abort_unless(auth()->user()->can('payments.print'), 403);

        $invoice->load(['items', 'customer', 'booking.bookingRooms.room', 'branch']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}
