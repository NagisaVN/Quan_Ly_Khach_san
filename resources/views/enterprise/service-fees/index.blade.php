@extends('layouts.app')
@section('content')
<x-adminlte-card><a href="{{ route('enterprise.service-fees.create') }}" class="btn btn-primary mb-3">Thêm</a>
@foreach($serviceFees as $f)<p><a href="{{ route('enterprise.service-fees.show', $f) }}">{{ $f->name }}</a></p>@endforeach</x-adminlte-card>
@endsection
