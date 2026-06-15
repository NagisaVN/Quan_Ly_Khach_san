@extends('layouts.app')

@section('title', 'Khách hàng')
@section('page-title', 'Khách hàng')

@section('content')
    <x-adminlte-card>
        <div class="d-flex justify-content-between mb-3">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
            </form>
            @can('create', App\Models\Customer::class)
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Thêm mới
                </a>
            @endcan
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th>Mã</th>
<th>Họ tên</th>
<th>Điện thoại</th>
<th>Email</th>

                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->code ?? '—' }}</td>
<td>{{ $customer->full_name }}</td>
<td>{{ $customer->phone ?? '—' }}</td>
<td>{{ $customer->email ?? '—' }}</td>

                            <td>
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('update', $customer)
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('delete', $customer)
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
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
        {{ $customers->links() }}
    </x-adminlte-card>
@endsection