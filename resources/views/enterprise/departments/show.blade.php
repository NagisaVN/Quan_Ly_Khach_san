@extends('layouts.app')
@section('content')
<x-adminlte-card><p>{{ $department->name }}</p><a href="{{ route('enterprise.departments.edit', $department) }}" class="btn btn-warning">Sửa</a></x-adminlte-card>
@endsection
