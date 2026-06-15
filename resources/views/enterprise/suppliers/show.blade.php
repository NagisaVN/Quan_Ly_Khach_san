@extends('layouts.app')
@section('content')
<x-adminlte-card><p>{{ $supplier->name }}</p><a href="{{ route('enterprise.suppliers.edit', $supplier) }}" class="btn btn-warning">Sửa</a></x-adminlte-card>
@endsection
