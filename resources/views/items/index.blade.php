@extends('layouts.app')

@section('title', 'Dịch vụ')
@section('page-title', 'Dịch vụ')

@section('content')
    <x-adminlte-card>
        <div class="d-flex justify-content-between mb-3">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
            </form>
            @can('create', App\Models\Service::class)
                <a href="{{ route('services.items.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Thêm mới
                </a>
            @endcan
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th>Tên</th>
<th>Danh mục</th>
<th>Đơn giá</th>
<th>Trạng thái</th>

                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td>{{ $service->name ?? '—' }}</td>
<td>{{ $service->category?->name ?? '—' }}</td>
<td>{{ number_format($service->unit_price, 0, ',', '.') }}đ</td>
<td>@if($service->is_active) <span class="badge text-bg-success">Hoạt động</span> @else <span class="badge text-bg-secondary">Ngừng</span> @endif</td>

                            <td>
                                <a href="{{ route('services.items.show', $service) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('update', $service)
                                    <a href="{{ route('services.items.edit', $service) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('delete', $service)
                                    <form action="{{ route('services.items.destroy', $service) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger btn-delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted">Chưa có dữ liệu</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $services->links() }}
    </x-adminlte-card>
@endsection