@extends('layouts.app')

@section('title', 'Báo cáo')
@section('page-title', 'Báo cáo thống kê')

@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate->toDateString() }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="to_date" class="form-control" value="{{ $toDate->toDateString() }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Nhóm theo</label>
                    <select name="group_by" class="form-select">
                        <option value="day" @selected($groupBy === 'day')>Ngày</option>
                        <option value="week" @selected($groupBy === 'week')>Tuần</option>
                        <option value="month" @selected($groupBy === 'month')>Tháng</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Áp dụng</button>
                </div>
                <div class="col-md-2">
                    @can('reports.export')
                        <a href="{{ route('reports.export.excel', request()->query()) }}" class="btn btn-outline-success w-100 mb-1">Excel</a>
                        <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-outline-danger w-100">PDF</a>
                    @endcan
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Doanh thu</h5>
                    <strong>{{ number_format($revenue['total'] ?? 0, 0, ',', '.') }} đ</strong>
                </div>
                <div class="card-body"><canvas id="revenueChart" height="200"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Công suất phòng</h5>
                    <strong>{{ $occupancy['average_occupancy'] ?? 0 }}%</strong>
                </div>
                <div class="card-body"><canvas id="occupancyChart" height="200"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Top dịch vụ</h5></div>
                <div class="card-body"><canvas id="servicesChart" height="200"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Top khách hàng</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Khách</th><th>Booking</th><th>Chi tiêu</th></tr></thead>
                        <tbody>
                            @forelse($topCustomers['customers'] ?? [] as $customer)
                                <tr>
                                    <td>{{ $customer['name'] }}</td>
                                    <td>{{ $customer['booking_count'] }}</td>
                                    <td>{{ number_format($customer['total_spent'], 0, ',', '.') }} đ</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted text-center">Không có dữ liệu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const revenueLabels = @json($revenue['labels'] ?? []);
const revenueData = @json($revenue['revenue'] ?? []);
const occupancyLabels = @json(collect($occupancy['data'] ?? [])->pluck('date'));
const occupancyData = @json(collect($occupancy['data'] ?? [])->pluck('rate_percent'));
const serviceLabels = @json($topServices['labels'] ?? []);
const serviceData = @json($topServices['revenue'] ?? []);

new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: { labels: revenueLabels, datasets: [{ label: 'Doanh thu', data: revenueData, borderColor: '#0d6efd', tension: 0.3 }] }
});
new Chart(document.getElementById('occupancyChart'), {
    type: 'bar',
    data: { labels: occupancyLabels, datasets: [{ label: 'Occupancy %', data: occupancyData, backgroundColor: '#198754' }] }
});
new Chart(document.getElementById('servicesChart'), {
    type: 'doughnut',
    data: { labels: serviceLabels, datasets: [{ data: serviceData, backgroundColor: ['#0d6efd','#6610f2','#6f42c1','#d63384','#fd7e14'] }] }
});
</script>
@endpush
