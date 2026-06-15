@extends('layouts.app')
@section('title', 'Cấu hình hệ thống')
@section('page-title', 'Cấu hình')
@section('content')
<x-adminlte-card><a href="{{ route('system.configs.create') }}" class="btn btn-primary mb-3">Thêm</a>
<table class="table"><tbody>@foreach($configs as $c)<tr><td>{{ $c->key }}</td><td>{{ Str::limit($c->value, 50) }}</td><td><a href="{{ route('system.configs.edit', $c) }}">Sửa</a></td></tr>@endforeach</tbody></table>{{ $configs->links() }}</x-adminlte-card>
@endsection
