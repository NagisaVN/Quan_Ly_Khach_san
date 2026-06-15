@extends('layouts.app')
@section('title', 'Nhà cung cấp')
@section('page-title', 'Nhà cung cấp')
@section('content')
<x-adminlte-card><a href="{{ route('enterprise.suppliers.create') }}" class="btn btn-primary mb-3">Thêm</a>
<table class="table"><tbody>@foreach($suppliers as $s)<tr><td><a href="{{ route('enterprise.suppliers.show', $s) }}">{{ $s->name }}</a></td><td>{{ $s->phone }}</td></tr>@endforeach</tbody></table>{{ $suppliers->links() }}</x-adminlte-card>
@endsection
