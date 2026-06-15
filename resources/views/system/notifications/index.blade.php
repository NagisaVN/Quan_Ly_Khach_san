@extends('layouts.app')
@section('title', 'Thông báo')
@section('page-title', 'Thông báo')
@section('content')
<x-adminlte-card>@forelse($notifications as $n)<div class="border-bottom py-2"><strong>{{ $n->title }}</strong><br>{{ $n->message }}</div>@empty<p class="text-muted">Không có thông báo</p>@endforelse{{ $notifications->links() }}</x-adminlte-card>
@endsection
