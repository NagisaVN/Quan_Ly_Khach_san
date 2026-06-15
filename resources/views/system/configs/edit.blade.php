@extends('layouts.app')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('system.configs.update', $config) }}">@csrf @method('PUT')<textarea name="value" class="form-control mb-3">{{ $config->value }}</textarea><button class="btn btn-primary">Lưu</button></form></x-adminlte-card>
@endsection
