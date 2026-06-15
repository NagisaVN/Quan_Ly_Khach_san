@extends('layouts.app')
@section('content')
<x-adminlte-card><p>{{ $tax->name }} — {{ $tax->rate }}%</p></x-adminlte-card>
@endsection
