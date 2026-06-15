@extends('layouts.app')

@section('title', 'Hóa đơn '.$invoice->invoice_number)
@section('page-title', 'Chi tiết hóa đơn')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $invoice->invoice_number }}</h5>
            <span class="badge text-bg-{{ $invoice->status->value === 'paid' ? 'success' : 'warning' }}">
                {{ $invoice->status->value }}
            </span>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Khách hàng:</strong> {{ $invoice->customer->full_name }}</p>
                    <p><strong>Ngày phát hành:</strong> {{ $invoice->issue_date?->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p><strong>Tổng:</strong> {{ number_format($invoice->total_amount, 0, ',', '.') }} đ</p>
                    <p><strong>Đã thanh toán:</strong> {{ number_format($invoice->paid_amount, 0, ',', '.') }} đ</p>
                    <p><strong>Còn lại:</strong> {{ number_format($invoice->balance, 0, ',', '.') }} đ</p>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr><th>Mô tả</th><th>SL</th><th>Đơn giá</th><th>Thành tiền</th></tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                            <td>{{ number_format($item->total_amount, 0, ',', '.') }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><th colspan="3" class="text-end">Subtotal</th><td>{{ number_format($invoice->subtotal, 0, ',', '.') }} đ</td></tr>
                    <tr><th colspan="3" class="text-end">Thuế</th><td>{{ number_format($invoice->tax_amount, 0, ',', '.') }} đ</td></tr>
                    <tr><th colspan="3" class="text-end">Tổng cộng</th><td><strong>{{ number_format($invoice->total_amount, 0, ',', '.') }} đ</strong></td></tr>
                </tfoot>
            </table>

            @if($invoice->payments->count())
                <h6>Lịch sử thanh toán</h6>
                <ul class="list-group mb-3">
                    @foreach($invoice->payments as $payment)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $payment->payment_number }} — {{ $payment->payment_method->value }}</span>
                            <span>{{ number_format($payment->amount, 0, ',', '.') }} đ</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="d-flex gap-2">
                @if($invoice->balance > 0)
                    @can('payments.create')
                        <a href="{{ route('payments.create', $invoice) }}" class="btn btn-primary">
                            <i class="fas fa-credit-card me-1"></i> Thanh toán
                        </a>
                    @endcan
                @endif
                @can('payments.print')
                    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-file-pdf me-1"></i> In PDF
                    </a>
                @endcan
                @if($invoice->booking)
                    <a href="{{ route('bookings.show', $invoice->booking) }}" class="btn btn-outline-primary">Về booking</a>
                @endif
            </div>
        </div>
    </div>
@endsection
