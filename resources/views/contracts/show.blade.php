@extends('layouts.app')
@section('title', 'Hợp đồng')
@section('page-title', '{{ $contract->contract_number }}')
@section('content')
<x-adminlte-card><p>{{ $contract->title }}</p><p>Trạng thái: {{ $contract->status }}</p><a href="{{ route('contracts.edit', $contract) }}" class="btn btn-warning">Sửa</a></x-adminlte-card>
@endsection
