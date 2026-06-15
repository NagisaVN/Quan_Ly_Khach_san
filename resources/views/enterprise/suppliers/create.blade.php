@extends('layouts.app')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('enterprise.suppliers.store') }}">@csrf<input name="name" class="form-control mb-3" required><input name="phone" class="form-control mb-3"><button class="btn btn-primary">Lưu</button></form></x-adminlte-card>
@endsection
