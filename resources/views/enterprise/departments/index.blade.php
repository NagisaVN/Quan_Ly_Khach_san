@extends('layouts.app')
@section('title', 'Phòng ban')
@section('page-title', 'Phòng ban')
@section('content')
<x-adminlte-card><a href="{{ route('enterprise.departments.create') }}" class="btn btn-primary mb-3">Thêm</a>
<table class="table"><tbody>@foreach($departments as $d)<tr><td><a href="{{ route('enterprise.departments.show', $d) }}">{{ $d->name }}</a></td></tr>@endforeach</tbody></table>{{ $departments->links() }}</x-adminlte-card>
@endsection
