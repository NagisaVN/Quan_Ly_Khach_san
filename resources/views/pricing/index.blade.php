@extends('layouts.app')

@section('title', 'Giá động')
@section('page-title', 'Quản lý giá động')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-7">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Quy tắc giá (Pricing Rules)</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Tên</th><th>Loại</th><th>Điều chỉnh</th><th>Ưu tiên</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse($rules as $rule)
                                <tr>
                                    <td>{{ $rule->name }}</td>
                                    <td>{{ $rule->type }}</td>
                                    <td>{{ $rule->adjustment_type === 'percent' ? $rule->value.'%' : number_format($rule->value, 0, ',', '.').' đ' }}</td>
                                    <td>{{ $rule->priority }}</td>
                                    <td>
                                        @can('pricing.delete')
                                            <form method="POST" action="{{ route('pricing-rules.destroy', $rule) }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted text-center">Chưa có quy tắc</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $rules->links() }}
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Giá theo mùa (Seasonal Rates)</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Tên</th><th>Loại phòng</th><th>Từ</th><th>Đến</th><th>Giá</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse($seasonalRates as $rate)
                                <tr>
                                    <td>{{ $rate->name }}</td>
                                    <td>{{ $rate->roomType->name }}</td>
                                    <td>{{ $rate->start_date->format('d/m/Y') }}</td>
                                    <td>{{ $rate->end_date->format('d/m/Y') }}</td>
                                    <td>{{ $rate->rate ? number_format($rate->rate, 0, ',', '.').' đ' : $rate->adjustment_percent.'%' }}</td>
                                    <td>
                                        @can('pricing.delete')
                                            <form method="POST" action="{{ route('pricing-rules.seasonal.destroy', $rate) }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-muted text-center">Chưa có giá theo mùa</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $seasonalRates->links('pagination::bootstrap-5', ['paginator' => $seasonalRates]) }}
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            @can('pricing.create')
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Thêm quy tắc giá</h5></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('pricing-rules.store') }}">
                            @csrf
                            <div class="mb-2">
                                <input type="text" name="name" class="form-control" placeholder="Tên quy tắc" required>
                            </div>
                            <div class="mb-2">
                                <select name="type" class="form-select" required>
                                    <option value="weekend">Weekend</option>
                                    <option value="holiday">Holiday</option>
                                    <option value="occupancy">Occupancy</option>
                                    <option value="loyalty">Loyalty</option>
                                    <option value="season">Season</option>
                                    <option value="event">Event</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <select name="room_type_id" class="form-select">
                                    <option value="">Tất cả loại phòng</option>
                                    @foreach($roomTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <select name="adjustment_type" class="form-select" required>
                                        <option value="percent">Phần trăm</option>
                                        <option value="fixed">Cố định</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <input type="number" name="value" class="form-control" placeholder="Giá trị" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="mb-2">
                                <input type="number" name="priority" class="form-control" placeholder="Ưu tiên" value="0">
                            </div>
                            <button class="btn btn-primary w-100">Lưu quy tắc</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Thêm giá theo mùa</h5></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('pricing-rules.seasonal.store') }}">
                            @csrf
                            <div class="mb-2">
                                <input type="text" name="name" class="form-control" placeholder="Tên mùa" required>
                            </div>
                            <div class="mb-2">
                                <select name="room_type_id" class="form-select" required>
                                    @foreach($roomTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><input type="date" name="start_date" class="form-control" required></div>
                                <div class="col-6"><input type="date" name="end_date" class="form-control" required></div>
                            </div>
                            <div class="mb-2">
                                <input type="number" name="rate" class="form-control" placeholder="Giá cố định (VND)" min="0">
                            </div>
                            <div class="mb-2">
                                <input type="number" name="adjustment_percent" class="form-control" placeholder="Hoặc % điều chỉnh" step="0.01">
                            </div>
                            <button class="btn btn-primary w-100">Lưu giá mùa</button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
