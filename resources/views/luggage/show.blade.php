@extends('layouts.app')
@section('title', 'Chi tiết hành lý')
@section('page-title', 'Hành lý {{ $luggage->tag_code }}')
@section('content')
<x-adminlte-card>
    <p>Khách: {{ $luggage->customer?->full_name }}</p>
    <p>Trạng thái: {{ $luggage->status }}</p>
    <a href="{{ route('luggage.edit', $luggage) }}" class="btn btn-warning">Sửa</a>
</x-adminlte-card>
@endsection
