@extends('layouts.app')
@section('title', 'Sửa hợp đồng')
@section('page-title', 'Sửa hợp đồng')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('contracts.update', $contract) }}">@csrf @method('PUT')
<div class="mb-3"><label>Tiêu đề</label><input name="title" class="form-control" value="{{ $contract->title }}" required></div>
<div class="mb-3"><label>Trạng thái</label><select name="status" class="form-select"><option value="active">Active</option><option value="expired">Expired</option></select></div>
<button class="btn btn-primary">Lưu</button></form></x-adminlte-card>
@endsection
