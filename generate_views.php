<?php

$modules = [
    ['companies', 'companies', 'company', 'Công ty', 'enterprise.companies', ['name' => 'Tên', 'code' => 'Mã', 'phone' => 'Điện thoại', 'is_active' => 'Trạng thái']],
    ['branches', 'branches', 'branch', 'Chi nhánh', 'enterprise.branches', ['name' => 'Tên', 'code' => 'Mã', 'company.name' => 'Công ty', 'is_active' => 'Trạng thái']],
    ['room-types', 'roomTypes', 'roomType', 'Loại phòng', 'rooms.room-types', ['name' => 'Tên', 'code' => 'Mã', 'base_price' => 'Giá cơ bản', 'is_active' => 'Trạng thái']],
    ['floors', 'floors', 'floor', 'Tầng', 'rooms.floors', ['name' => 'Tên', 'floor_number' => 'Số tầng', 'is_active' => 'Trạng thái']],
    ['rooms', 'rooms', 'room', 'Phòng', 'rooms.rooms', ['room_number' => 'Số phòng', 'roomType.name' => 'Loại', 'floor.name' => 'Tầng', 'status' => 'Trạng thái']],
    ['amenities', 'amenities', 'amenity', 'Tiện ích', 'rooms.amenities', ['name' => 'Tên', 'icon' => 'Icon', 'is_active' => 'Trạng thái']],
    ['customers', 'customers', 'customer', 'Khách hàng', 'customers', ['code' => 'Mã', 'full_name' => 'Họ tên', 'phone' => 'Điện thoại', 'email' => 'Email']],
    ['categories', 'serviceCategories', 'serviceCategory', 'Danh mục dịch vụ', 'services.categories', ['name' => 'Tên', 'code' => 'Mã', 'is_active' => 'Trạng thái']],
    ['items', 'services', 'service', 'Dịch vụ', 'services.items', ['name' => 'Tên', 'category.name' => 'Danh mục', 'unit_price' => 'Đơn giá', 'is_active' => 'Trạng thái']],
    ['users', 'users', 'user', 'Người dùng', 'system.users', ['name' => 'Tên', 'email' => 'Email', 'roles' => 'Vai trò', 'is_active' => 'Trạng thái']],
];

foreach ($modules as [$folder, $collection, $var, $label, $routePrefix, $columns]) {
    if (! is_dir("resources/views/{$folder}")) {
        mkdir("resources/views/{$folder}", 0777, true);
    }

    $headerCols = '';
    $bodyCols = '';
    foreach ($columns as $field => $colLabel) {
        $headerCols .= "<th>{$colLabel}</th>\n";
        if ($field === 'is_active') {
            $bodyCols .= "<td>@if(\${$var}->{$field}) <span class=\"badge text-bg-success\">Hoạt động</span> @else <span class=\"badge text-bg-secondary\">Ngừng</span> @endif</td>\n";
        } elseif ($field === 'status') {
            $bodyCols .= "<td><span class=\"badge text-bg-info\">{{ \${$var}->{$field}?->value ?? \${$var}->{$field} }}</span></td>\n";
        } elseif ($field === 'base_price' || $field === 'unit_price') {
            $bodyCols .= "<td>{{ number_format(\${$var}->{$field}, 0, ',', '.') }}đ</td>\n";
        } elseif ($field === 'full_name') {
            $bodyCols .= "<td>{{ \${$var}->full_name }}</td>\n";
        } elseif ($field === 'roles') {
            $bodyCols .= "<td>{{ \${$var}->roles->pluck('name')->join(', ') }}</td>\n";
        } elseif (str_contains($field, '.')) {
            [$rel, $attr] = explode('.', $field);
            $bodyCols .= "<td>{{ \${$var}->{$rel}?->{$attr} ?? '—' }}</td>\n";
        } else {
            $bodyCols .= "<td>{{ \${$var}->{$field} ?? '—' }}</td>\n";
        }
    }

    $index = <<<BLADE
@extends('layouts.app')

@section('title', '{$label}')
@section('page-title', '{$label}')

@section('content')
    <x-adminlte-card>
        <div class="d-flex justify-content-between mb-3">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
            </form>
            @can('create', App\Models\\{$var}::class)
                <a href="{{ route('{$routePrefix}.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Thêm mới
                </a>
            @endcan
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover datatable">
                <thead>
                    <tr>
                        {$headerCols}
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(\${$collection} as \${$var})
                        <tr>
                            {$bodyCols}
                            <td>
                                <a href="{{ route('{$routePrefix}.show', \${$var}) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('update', \${$var})
                                    <a href="{{ route('{$routePrefix}.edit', \${$var}) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('delete', \${$var})
                                    <form action="{{ route('{$routePrefix}.destroy', \${$var}) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger btn-delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ count([$columns]) + 1 }}" class="text-center text-muted">Chưa có dữ liệu</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ \${$collection}->links() }}
    </x-adminlte-card>
@endsection
BLADE;

    // Fix model class name in index - need proper casing
    $modelClass = match ($var) {
        'roomType' => 'RoomType',
        'serviceCategory' => 'ServiceCategory',
        'service' => 'Service',
        default => ucfirst($var),
    };
    $index = str_replace("App\Models\\{$var}", "App\\Models\\{$modelClass}", $index);
    $index = str_replace('count([$columns])', (string) count($columns), $index);

    file_put_contents("resources/views/{$folder}/index.blade.php", $index);

    $showFields = '';
    foreach ($columns as $field => $colLabel) {
        if ($field === 'roles') {
            $showFields .= "<dt class=\"col-sm-3\">{$colLabel}</dt><dd class=\"col-sm-9\">{{ \${$var}->roles->pluck('name')->join(', ') }}</dd>\n";
        } elseif ($field === 'full_name') {
            $showFields .= "<dt class=\"col-sm-3\">{$colLabel}</dt><dd class=\"col-sm-9\">{{ \${$var}->full_name }}</dd>\n";
        } elseif (str_contains($field, '.')) {
            [$rel, $attr] = explode('.', $field);
            $showFields .= "<dt class=\"col-sm-3\">{$colLabel}</dt><dd class=\"col-sm-9\">{{ \${$var}->{$rel}?->{$attr} ?? '—' }}</dd>\n";
        } elseif ($field === 'is_active') {
            $showFields .= "<dt class=\"col-sm-3\">{$colLabel}</dt><dd class=\"col-sm-9\">@if(\${$var}->{$field}) <span class=\"badge text-bg-success\">Hoạt động</span> @else <span class=\"badge text-bg-secondary\">Ngừng</span> @endif</dd>\n";
        } else {
            $showFields .= "<dt class=\"col-sm-3\">{$colLabel}</dt><dd class=\"col-sm-9\">{{ \${$var}->{$field} ?? '—' }}</dd>\n";
        }
    }

    $show = <<<BLADE
@extends('layouts.app')

@section('title', 'Chi tiết {$label}')
@section('page-title', 'Chi tiết {$label}')

@section('content')
    <x-adminlte-card>
        <dl class="row">
            {$showFields}
        </dl>
        <div class="mt-3">
            @can('update', \${$var})
                <a href="{{ route('{$routePrefix}.edit', \${$var}) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Sửa</a>
            @endcan
            <a href="{{ route('{$routePrefix}.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
        </div>
    </x-adminlte-card>
@endsection
BLADE;
    file_put_contents("resources/views/{$folder}/show.blade.php", $show);
}

echo "Base views generated\n";
