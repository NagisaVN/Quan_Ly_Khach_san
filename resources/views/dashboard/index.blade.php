@extends('layouts.app')

@section('title', 'Bảng điều khiển')
@section('page-title', 'Bảng điều khiển')

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <div class="callout callout-info mb-0">
                <h5 class="mb-1"><i class="fas fa-hand-sparkles me-2"></i>Xin chào, {{ auth()->user()->name }}!</h5>
                <p class="mb-0 text-muted">Tổng quan vận hành khách sạn hôm nay — {{ now()->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h3>{{ number_format($kpis['available_rooms']) }}<small class="fs-6">/{{ $kpis['total_rooms'] }}</small></h3>
                    <p>Phòng trống</p>
                </div>
                <div class="icon"><i class="fas fa-door-open"></i></div>
                <a href="{{ Route::has('rooms.rooms.index') ? route('rooms.rooms.index') : '#' }}" class="small-box-footer">Xem phòng <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h3>{{ number_format($kpis['check_ins_today']) }}</h3>
                    <p>Check-in hôm nay</p>
                </div>
                <div class="icon"><i class="fas fa-sign-in-alt"></i></div>
                <span class="small-box-footer">Lễ tân xử lý</span>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-warning">
                <div class="inner">
                    <h3>{{ number_format($kpis['check_outs_today']) }}</h3>
                    <p>Check-out hôm nay</p>
                </div>
                <div class="icon"><i class="fas fa-sign-out-alt"></i></div>
                <span class="small-box-footer">Cần thanh toán</span>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-danger">
                <div class="inner">
                    <h3>{{ number_format($kpis['revenue_today'], 0, ',', '.') }}<small class="fs-6">đ</small></h3>
                    <p>Doanh thu hôm nay</p>
                </div>
                <div class="icon"><i class="fas fa-chart-line"></i></div>
                <span class="small-box-footer">Lấp đầy: {{ $kpis['occupancy_rate'] }}%</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <x-adminlte-card title="Doanh thu 7 ngày qua" icon="fas fa-chart-line">
                <canvas id="revenueChart" height="120"></canvas>
            </x-adminlte-card>
        </div>
        <div class="col-lg-4">
            <x-adminlte-card title="Trạng thái đặt phòng" icon="fas fa-chart-pie">
                <canvas id="bookingStatusChart" height="200"></canvas>
            </x-adminlte-card>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <x-adminlte-card title="Tỷ lệ lấp đầy (7 ngày)" icon="fas fa-percentage">
                <canvas id="occupancyChart" height="160"></canvas>
            </x-adminlte-card>
        </div>
        <div class="col-lg-6">
            <x-adminlte-card title="Đặt phòng gần đây" icon="fas fa-calendar-alt">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Mã</th>
                                <th>Khách</th>
                                <th>Ngày</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                                <tr>
                                    <td><code>{{ $booking->booking_code }}</code></td>
                                    <td>{{ $booking->customer?->full_name ?? '—' }}</td>
                                    <td>{{ $booking->check_in_date?->format('d/m') }} - {{ $booking->check_out_date?->format('d/m') }}</td>
                                    <td><span class="badge text-bg-secondary">{{ $booking->status->value }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center">Chưa có đặt phòng</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-adminlte-card>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: @json($revenueChart['labels']),
            datasets: [{ label: 'Doanh thu (VND)', data: @json($revenueChart['data']), borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,0.1)', fill: true, tension: 0.3 }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('occupancyChart'), {
        type: 'bar',
        data: {
            labels: @json($occupancyChart['labels']),
            datasets: [{ label: 'Lấp đầy (%)', data: @json($occupancyChart['data']), backgroundColor: '#198754' }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100 } } }
    });

    new Chart(document.getElementById('bookingStatusChart'), {
        type: 'doughnut',
        data: {
            labels: @json($bookingStatusChart['labels']),
            datasets: [{ data: @json($bookingStatusChart['data']), backgroundColor: ['#6c757d','#0d6efd','#198754','#ffc107','#dc3545','#212529'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endpush
