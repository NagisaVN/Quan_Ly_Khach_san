@extends('layouts.app')
@section('content')
<x-adminlte-card><a href="{{ route('enterprise.bank-accounts.create') }}" class="btn btn-primary mb-3">Thêm</a>
@foreach($bankAccounts as $a)<p><a href="{{ route('enterprise.bank-accounts.show', $a) }}">{{ $a->bank_name }} — {{ $a->account_number }}</a></p>@endforeach
{{ $bankAccounts->links() }}</x-adminlte-card>
@endsection
