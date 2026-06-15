@extends('layouts.app')
@section('title', 'Nhật ký hệ thống')
@section('page-title', 'Activity logs')
@section('content')
<x-adminlte-card><table class="table"><thead><tr><th>Action</th><th>User</th><th>Thời gian</th></tr></thead>
<tbody>@foreach($logs as $log)<tr><td>{{ $log->action ?? $log->description ?? '—' }}</td><td>{{ $log->user_id }}</td><td>{{ $log->created_at }}</td></tr>@endforeach</tbody></table>{{ $logs->links() }}</x-adminlte-card>
@endsection
