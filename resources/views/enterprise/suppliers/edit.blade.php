@extends('layouts.app')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('enterprise.suppliers.update', $supplier) }}">@csrf @method('PUT')<input name="name" class="form-control mb-3" value="{{ $supplier->name }}"><button class="btn btn-primary">Lưu</button></form></x-adminlte-card>
@endsection
