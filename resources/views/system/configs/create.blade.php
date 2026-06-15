@extends('layouts.app')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('system.configs.store') }}">@csrf<input name="key" class="form-control mb-3" required><textarea name="value" class="form-control mb-3"></textarea><button class="btn btn-primary">Lưu</button></form></x-adminlte-card>
@endsection
