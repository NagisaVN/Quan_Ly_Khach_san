@extends('layouts.app')
@section('content')
<x-adminlte-card><p>{{ $serviceFee->name }} — {{ $serviceFee->value }}</p></x-adminlte-card>
@endsection
