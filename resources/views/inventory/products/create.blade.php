@extends('layouts.app')
@section('title', 'Thêm sản phẩm')
@section('page-title', 'Thêm sản phẩm')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('inventory.products.store') }}">@csrf
<div class="mb-3"><label>Tên</label><input name="name" class="form-control" required></div>
<div class="mb-3"><label>SKU</label><input name="sku" class="form-control" required></div>
<div class="mb-3"><label>Tồn kho</label><input name="stock_quantity" type="number" class="form-control" value="0"></div>
<button class="btn btn-primary">Lưu</button></form></x-adminlte-card>
@endsection
