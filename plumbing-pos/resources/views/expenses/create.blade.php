
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Record Expense</h1>

    @include('expenses.form', [
        'expense' => null,
        'action' => route('expenses.store'),
        'method' => 'POST'
    ])
</div>
@endsection