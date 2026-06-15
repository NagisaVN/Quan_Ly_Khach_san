@extends('layouts.app')

@section('title', 'Chi tiết Dịch vụ')
@section('page-title', 'Chi tiết Dịch vụ')

@section('content')
    <x-adminlte-card>
        <dl class="row">
            <dt class="col-sm-3">Tên</dt><dd class="col-sm-9">{{ $service->name ?? '—' }}</dd>
<dt class="col-sm-3">Danh mục</dt><dd class="col-sm-9">{{ $service->category?->name ?? '—' }}</dd>
<dt class="col-sm-3">Đơn giá</dt><dd class="col-sm-9">{{ $service->unit_price ?? '—' }}</dd>
<dt class="col-sm-3">Trạng thái</dt><dd class="col-sm-9">@if($service->is_active) <span class="badge text-bg-success">Hoạt động</span> @else <span class="badge text-bg-secondary">Ngừng</span> @endif</dd>

        </dl>
        <div class="mt-3">
            @can('update', $service)
                <a href="{{ route('services.items.edit', $service) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Sửa</a>
            @endcan
            <a href="{{ route('services.items.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
        </div>
    </x-adminlte-card>
@endsection