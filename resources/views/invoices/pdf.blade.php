<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Hóa đơn {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .muted { color: #666; }
    </style>
</head>
<body>
    <h1>HÓA ĐƠN THANH TOÁN</h1>
    <p class="muted">{{ $invoice->branch->name ?? 'Hotel MS' }}</p>
    <p><strong>Số HĐ:</strong> {{ $invoice->invoice_number }}</p>
    <p><strong>Ngày:</strong> {{ $invoice->issue_date?->format('d/m/Y') }}</p>
    <p><strong>Khách hàng:</strong> {{ $invoice->customer->full_name }}</p>

    <table>
        <thead>
            <tr>
                <th>Mô tả</th>
                <th>SL</th>
                <th class="text-right">Đơn giá</th>
                <th class="text-right">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->total_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="3" class="text-right"><strong>Subtotal</strong></td><td class="text-right">{{ number_format($invoice->subtotal, 0, ',', '.') }}</td></tr>
            <tr><td colspan="3" class="text-right"><strong>Thuế</strong></td><td class="text-right">{{ number_format($invoice->tax_amount, 0, ',', '.') }}</td></tr>
            <tr><td colspan="3" class="text-right"><strong>Tổng cộng</strong></td><td class="text-right"><strong>{{ number_format($invoice->total_amount, 0, ',', '.') }}</strong></td></tr>
            <tr><td colspan="3" class="text-right">Đã thanh toán</td><td class="text-right">{{ number_format($invoice->paid_amount, 0, ',', '.') }}</td></tr>
            <tr><td colspan="3" class="text-right">Còn lại</td><td class="text-right">{{ number_format($invoice->balance, 0, ',', '.') }}</td></tr>
        </tfoot>
    </table>
</body>
</html>
