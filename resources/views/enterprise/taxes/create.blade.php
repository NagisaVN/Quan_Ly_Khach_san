@extends('layouts.app')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('enterprise.taxes.store') }}">@csrf
<input name="name" class="form-control mb-2" required><input name="code" class="form-control mb-2" required>
<input name="rate" type="number" step="0.01" class="form-control mb-2" required><button class="btn btn-primary">Lưu</button></form></x-adminlte-card>
@endsection
