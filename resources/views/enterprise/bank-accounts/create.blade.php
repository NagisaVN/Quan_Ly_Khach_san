@extends('layouts.app')
@section('content')
<x-adminlte-card><form method="POST" action="{{ route('enterprise.bank-accounts.store') }}">@csrf
<input name="bank_name" class="form-control mb-2" placeholder="Ngân hàng" required>
<input name="account_number" class="form-control mb-2" placeholder="Số TK" required>
<input name="account_holder" class="form-control mb-2" placeholder="Chủ TK" required>
<button class="btn btn-primary">Lưu</button></form></x-adminlte-card>
@endsection
