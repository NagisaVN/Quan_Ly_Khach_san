<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Báo cáo {{ $fromDate->format('d/m/Y') }} - {{ $toDate->format('d/m/Y') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 5px 8px; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>BÁO CÁO TỔNG HỢP</h1>
    <p>Từ {{ $fromDate->format('d/m/Y') }} đến {{ $toDate->format('d/m/Y') }}</p>
    <p><strong>Tổng doanh thu:</strong> {{ number_format($revenue['total'] ?? 0, 0, ',', '.') }} VND</p>
    <p><strong>Công suất trung bình:</strong> {{ $occupancy['average_occupancy'] ?? 0 }}%</p>

    <h2>Doanh thu theo kỳ</h2>
    <table>
        <thead><tr><th>Kỳ</th><th>Doanh thu</th></tr></thead>
        <tbody>
            @foreach($revenue['labels'] ?? [] as $i => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ number_format($revenue['revenue'][$i] ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
