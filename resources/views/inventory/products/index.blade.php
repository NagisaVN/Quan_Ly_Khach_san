@extends('layouts.app')
@section('title', 'Kho hàng')
@section('page-title', 'Sản phẩm kho')
@section('content')
<x-adminlte-card>
<a href="{{ route('inventory.products.create') }}" class="btn btn-primary mb-3">Thêm sản phẩm</a>
<table class="table"><thead><tr><th>SKU</th><th>Tên</th><th>Tồn</th><th></th></tr></thead>
<tbody>@foreach($products as $p)<tr><td>{{ $p->sku }}</td><td>{{ $p->name }}</td><td>{{ $p->stock_quantity }}</td><td><a href="{{ route('inventory.products.show', $p) }}" class="btn btn-sm btn-info">Xem</a></td></tr>@endforeach</tbody></table>
{{ $products->links() }}
</x-adminlte-card>
@endsection
