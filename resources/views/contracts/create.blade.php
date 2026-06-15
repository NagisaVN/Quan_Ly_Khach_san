@extends('layouts.app')
@section('title', 'Thêm hợp đồng')
@section('page-title', 'Thêm hợp đồng')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('contracts.store') }}">@csrf
<div class="mb-3"><label>Tiêu đề</label><input name="title" class="form-control" required></div>
<div class="mb-3"><label>Bắt đầu</label><input type="date" name="start_date" class="form-control" required></div>
<div class="mb-3"><label>Kết thúc</label><input type="date" name="end_date" class="form-control"></div>
<button class="btn btn-primary">Lưu</button></form></x-adminlte-card>
@endsection
