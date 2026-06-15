@extends('layouts.app')
@section('title', 'Chi tiết khách hàng')
@section('page-title', 'Chi tiết khách hàng')
@section('content')
<x-adminlte-card>
<dl class="row">
<dt class="col-sm-3">Mã</dt><dd class="col-sm-9">{{ $customer->code ?? '—' }}</dd>
<dt class="col-sm-3">Họ tên</dt><dd class="col-sm-9">{{ $customer->full_name }}</dd>
<dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $customer->email ?? '—' }}</dd>
<dt class="col-sm-3">Điện thoại</dt><dd class="col-sm-9">{{ $customer->phone ?? '—' }}</dd>
<dt class="col-sm-3">CCCD/CMND</dt><dd class="col-sm-9">{{ $customer->id_number ?? '—' }}</dd>
<dt class="col-sm-3">Điểm loyalty</dt><dd class="col-sm-9">{{ number_format($customer->loyalty_points) }}</dd>
</dl>
@if($customer->documents->isNotEmpty())
<h6 class="mt-3">Tài liệu đính kèm</h6>
<div class="row g-2">
@foreach($customer->documents as $doc)
<div class="col-md-3"><img src="{{ asset('storage/'.$doc->path) }}" class="img-thumbnail" alt="CCCD"></div>
@endforeach
</div>
@endif
<div class="mt-3">
@can('update', $customer)<a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Sửa</a>@endcan
<a href="{{ route('customers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
</div>
</x-adminlte-card>
@endsection
