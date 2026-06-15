@extends('layouts.app')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('enterprise.departments.store') }}">@csrf<input name="name" class="form-control mb-3" placeholder="Tên phòng ban" required><button class="btn btn-primary">Lưu</button></form></x-adminlte-card>
@endsection
