@extends('layouts.app')

@section('title', 'Công ty')
@section('page-title', 'Công ty')

@section('content')
    <x-adminlte-card>
        <div class="d-flex justify-content-between mb-3">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
            </form>
            @can('create', App\Models\Company::class)
                <a href="{{ route('enterprise.companies.create') }}" class="btn btn-primary">
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
<th>Điện thoại</th>
<th>Trạng thái</th>

                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr>
                            <td>{{ $company->name ?? '—' }}</td>
<td>{{ $company->code ?? '—' }}</td>
<td>{{ $company->phone ?? '—' }}</td>
<td>@if($company->is_active) <span class="badge text-bg-success">Hoạt động</span> @else <span class="badge text-bg-secondary">Ngừng</span> @endif</td>

                            <td>
                                <a href="{{ route('enterprise.companies.show', $company) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('update', $company)
                                    <a href="{{ route('enterprise.companies.edit', $company) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('delete', $company)
                                    <form action="{{ route('enterprise.companies.destroy', $company) }}" method="POST" class="d-inline">
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
        {{ $companies->links() }}
    </x-adminlte-card>
@endsection