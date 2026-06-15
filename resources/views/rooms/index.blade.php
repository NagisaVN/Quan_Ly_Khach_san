@extends('layouts.app')

@section('title', 'Phòng')
@section('page-title', 'Phòng')

@section('content')
    <x-adminlte-card>
        <div class="d-flex justify-content-between mb-3">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
            </form>
            @can('create', App\Models\Room::class)
                <a href="{{ route('rooms.rooms.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Thêm mới
                </a>
            @endcan
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th>Số phòng</th>
<th>Loại</th>
<th>Tầng</th>
<th>Trạng thái</th>

                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr>
                            <td>{{ $room->room_number ?? '—' }}</td>
<td>{{ $room->roomType?->name ?? '—' }}</td>
<td>{{ $room->floor?->name ?? '—' }}</td>
<td><span class="badge text-bg-info">{{ $room->status?->value ?? $room->status }}</span></td>

                            <td>
                                <a href="{{ route('rooms.rooms.show', $room) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('update', $room)
                                    <a href="{{ route('rooms.rooms.edit', $room) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('delete', $room)
                                    <form action="{{ route('rooms.rooms.destroy', $room) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger btn-delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ count([Array]) + 1 }}" class="text-center text-muted">Chưa có dữ liệu</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $rooms->links() }}
    </x-adminlte-card>
@endsection