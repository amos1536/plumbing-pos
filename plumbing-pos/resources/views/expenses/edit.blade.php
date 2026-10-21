
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Expense</h1>

    @include('expenses.form', [
        'expense' => $expense,
        'action' => route('expenses.update', $expense),
        'method' => 'PUT'
    ])
</div>
@endsection