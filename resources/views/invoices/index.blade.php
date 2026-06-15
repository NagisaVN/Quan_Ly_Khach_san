@extends('layouts.app')

@section('title', 'Hóa đơn')
@section('page-title', 'Quản lý hóa đơn')

@section('content')
    <x-adminlte-card>
        <div class="d-flex justify-content-between mb-3">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Tìm số HĐ, SĐT..." value="{{ request('search') }}">
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    @foreach(['draft', 'issued', 'partial', 'paid', 'cancelled'] as $st)
                        <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th>Số HĐ</th>
                        <th>Khách hàng</th>
                        <th>Booking</th>
                        <th>Tổng tiền</th>
                        <th>Còn lại</th>
                        <th>Trạng thái</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->customer?->full_name ?? '—' }}</td>
                            <td>{{ $invoice->booking?->booking_code ?? '—' }}</td>
                            <td>{{ number_format($invoice->total_amount, 0, ',', '.') }}đ</td>
                            <td>{{ number_format($invoice->balance, 0, ',', '.') }}đ</td>
                            <td><span class="badge text-bg-info">{{ $invoice->status?->value ?? $invoice->status }}</span></td>
                            <td>
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('payments.print')
                                    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-secondary"><i class="fas fa-file-pdf"></i></a>
                                @endcan
                                @if((float) $invoice->balance > 0 && auth()->user()->can('payments.create'))
                                    <a href="{{ route('payments.create', $invoice) }}" class="btn btn-sm btn-success"><i class="fas fa-money-bill"></i></a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Chưa có hóa đơn</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $invoices->links() }}
    </x-adminlte-card>
@endsection
