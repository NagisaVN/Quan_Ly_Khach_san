@extends('layouts.app')

@section('title', 'Loại phòng')
@section('page-title', 'Loại phòng')

@section('content')
    <x-adminlte-card>
        <div class="d-flex justify-content-between mb-3">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
            </form>
            @can('create', App\Models\RoomType::class)
                <a href="{{ route('rooms.room-types.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Thêm mới
                </a>
            @endcan
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th>Tên</th>
<th>Mã</th>
<th>Giá cơ bản</th>
<th>Trạng thái</th>

                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roomTypes as $roomType)
                        <tr>
                            <td>{{ $roomType->name ?? '—' }}</td>
<td>{{ $roomType->code ?? '—' }}</td>
<td>{{ number_format($roomType->base_price, 0, ',', '.') }}đ</td>
<td>@if($roomType->is_active) <span class="badge text-bg-success">Hoạt động</span> @else <span class="badge text-bg-secondary">Ngừng</span> @endif</td>

                            <td>
                                <a href="{{ route('rooms.room-types.show', $roomType) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('update', $roomType)
                                    <a href="{{ route('rooms.room-types.edit', $roomType) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('delete', $roomType)
                                    <form action="{{ route('rooms.room-types.destroy', $roomType) }}" method="POST" class="d-inline">
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
        {{ $roomTypes->links() }}
    </x-adminlte-card>
@endsection