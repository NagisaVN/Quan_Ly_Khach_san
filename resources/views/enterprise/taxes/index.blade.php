@extends('layouts.app')
@section('content')
<x-adminlte-card><a href="{{ route('enterprise.taxes.create') }}" class="btn btn-primary mb-3">Thêm</a>
@foreach($taxes as $t)<p><a href="{{ route('enterprise.taxes.show', $t) }}">{{ $t->name }} ({{ $t->rate }}%)</a></p>@endforeach</x-adminlte-card>
@endsection
