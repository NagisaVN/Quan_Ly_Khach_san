@extends('layouts.app')
@section('title', 'Backup')
@section('page-title', 'Sao lưu dữ liệu')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('system.backups.store') }}">@csrf<button class="btn btn-primary mb-3">Tạo backup (mock)</button></form>
<table class="table"><tbody>@foreach($backups as $b)<tr><td>{{ $b->filename }}</td><td>{{ $b->status }}</td><td>{{ $b->created_at }}</td></tr>@endforeach</tbody></table>{{ $backups->links() }}
</x-adminlte-card>
@endsection
