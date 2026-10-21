
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Expenses Management</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p>
        <strong>Total Expenses:</strong>
        KSh {{ number_format($totalExpenses, 2) }}
    </p>

    <p>
        <a href="{{ route('expenses.create') }}" class="btn">
            + Record Expense
        </a>
    </p>

    <form method="GET" action="{{ route('expenses.index') }}"
          style="display:flex; gap:12px; flex-wrap:wrap; align-items:end; margin-bottom:20px;">

        <div>
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date"
                   value="{{ request('start_date') }}">
        </div>

        <div>
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date"
                   value="{{ request('end_date') }}"
                   min="{{ request('start_date') }}">
        </div>

        <div>
            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}"
                        @selected(request('category') === $category)>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn">Filter</button>
        <a href="{{ route('expenses.index') }}" class="btn-secondary">
            Clear
        </a>
    </form>

    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Payment</th>
                    <th>Amount (KSh)</th>
                    <th>Recorded By</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->expense_date->format('d M Y') }}</td>
                        <td>{{ $expense->title }}</td>
                        <td>{{ $expense->category }}</td>
                        <td>{{ $expense->payment_method }}</td>
                        <td>{{ number_format($expense->amount, 2) }}</td>
                        <td>{{ $expense->user?->name ?? 'Unknown' }}</td>
                        <td>
                            <a href="{{ route('expenses.edit', $expense) }}">
                                Edit
                            </a>

                            <form method="POST"
                                  action="{{ route('expenses.destroy', $expense) }}"
                                  onsubmit="return confirm('Are you sure you want to delete this expense?');"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        style="color:#b91c1c; background:none; border:none; cursor:pointer;">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No expenses found for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $expenses->links() }}
</div>
@endsection