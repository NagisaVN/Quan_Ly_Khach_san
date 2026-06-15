@extends('layouts.app')
@section('title', 'Sửa sản phẩm')
@section('page-title', 'Sửa sản phẩm')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('inventory.products.update', $product) }}">@csrf @method('PUT')
<div class="mb-3"><label>Tên</label><input name="name" class="form-control" value="{{ $product->name }}" required></div>
<div class="mb-3"><label>Điều chỉnh tồn (+/-)</label><input name="stock_adjustment" type="number" class="form-control" value="0"></div>
<button class="btn btn-primary">Cập nhật</button></form></x-adminlte-card>
@endsection
