@extends('layouts.app')
@section('content')
<x-adminlte-card><p>{{ $bankAccount->bank_name }} — {{ $bankAccount->account_number }}</p></x-adminlte-card>
@endsection
