@extends('layouts.app')

@section('title', 'Thanh toán')
@section('page-title', 'Thanh toán hóa đơn')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thanh toán {{ $invoice->invoice_number }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Khách hàng:</strong> {{ $invoice->customer->full_name }}</p>
                    <p><strong>Số tiền còn lại:</strong> {{ number_format($invoice->balance, 0, ',', '.') }} đ</p>

                    <form method="POST" action="{{ route('payments.store', $invoice) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Số tiền</label>
                            <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount', $invoice->balance) }}" min="0.01" step="0.01" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phương thức</label>
                            <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                @foreach(['cash','bank','momo','vnpay','qr'] as $method)
                                    <option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ strtoupper($method) }}</option>
                                @endforeach
                            </select>
                            @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mã tham chiếu</label>
                            <input type="text" name="reference" class="form-control" value="{{ old('reference') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Xác nhận thanh toán</button>
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
