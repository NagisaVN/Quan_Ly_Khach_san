@extends('layouts.app')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('enterprise.departments.update', $department) }}">@csrf @method('PUT')<input name="name" class="form-control mb-3" value="{{ $department->name }}"><button class="btn btn-primary">Lưu</button></form></x-adminlte-card>
@endsection
