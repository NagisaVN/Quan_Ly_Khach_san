@extends('layouts.app')
@section('title', 'Sản phẩm')
@section('page-title', '{{ $product->name }}')
@section('content')
<x-adminlte-card>
<p>SKU: {{ $product->sku }} | Tồn: {{ $product->stock_quantity }}</p>
<a href="{{ route('inventory.products.edit', $product) }}" class="btn btn-warning">Sửa</a>
</x-adminlte-card>
@endsection
