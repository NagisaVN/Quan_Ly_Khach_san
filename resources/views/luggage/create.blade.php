@extends('layouts.app')
@section('title', 'Thêm hành lý')
@section('page-title', 'Thêm hành lý')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('luggage.store') }}">@csrf
    <div class="mb-3"><label>Khách hàng</label><select name="customer_id" class="form-select" required>@foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->full_name }}</option>@endforeach</select></div>
    <div class="mb-3"><label>Số lượng</label><input type="number" name="quantity" class="form-control" value="1" min="1"></div>
    <div class="mb-3"><label>Vị trí</label><input type="text" name="storage_location" class="form-control"></div>
    <button class="btn btn-primary">Lưu</button>
</form>
</x-adminlte-card>
@endsection
