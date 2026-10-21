@extends('layouts.app')

@section('title', 'Sales Reports - Plumbing POS')
@section('page-heading', 'Sales Reports')
@section('page-description', 'Review sales performance for a selected period')

@section('content')

{{-- Date Filters --}}
<div class="card">
    <h2>Filter Sales by Date</h2>

    @if($errors->any())
        <div class="alert alert-error">
            <strong>Please correct the following:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reports.sales') }}"
          method="GET"
          style="display: flex; gap: 16px; flex-wrap: wrap; align-items: end;">

        <div>
            <label for="start_date">Start Date</label>
            <input
                type="date"
                id="start_date"
                name="start_date"
                value="{{ $startDate }}"
                max="{{ $endDate }}"
                required
            >
        </div>

        <div>
            <label for="end_date">End Date</label>
            <input
                type="date"
                id="end_date"
                name="end_date"
                value="{{ $endDate }}"
                min="{{ $startDate }}"
                required
            >
        </div>

        <button type="submit" class="btn">
            Generate Report
        </button>

        <a href="{{ route('reports.sales') }}" class="btn-secondary">
            Today
        </a>
    </form>
</div>

{{-- Summary Cards --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-top: 24px;">

    {{-- Total Revenue --}}
    <div class="card">
        <p class="text-muted">Total Revenue</p>
        <h2>KSh {{ number_format((float) $totalRevenue, 2) }}</h2>
        <p class="text-muted">Completed sales for the selected period</p>
    </div>

    {{-- Completed Transactions --}}
    <div class="card">
        <p class="text-muted">Completed Transactions</p>
        <h2>{{ number_format($totalTransactions) }}</h2>
        <p class="text-muted">Number of completed sales</p>
    </div>

    {{-- Cost of Goods Sold --}}
    <div class="card">
        <p class="text-muted">Cost of Goods Sold</p>
        <h2>KSh {{ number_format((float) $totalCost, 2) }}</h2>
        <p class="text-muted">Cost of products sold</p>
    </div>

    {{-- Gross Profit --}}
    <div class="card">
        <p class="text-muted">Estimated Gross Profit</p>
        <h2>KSh {{ number_format((float) $grossProfit, 2) }}</h2>
        <p class="text-muted">Revenue minus cost of goods sold</p>
    </div>

    {{-- Total Expenses: NEW --}}
    <div class="card">
        <p class="text-muted">Total Expenses</p>
        <h2>KSh {{ number_format((float) $totalExpenses, 2) }}</h2>
        <p class="text-muted">Recorded expenses for this period</p>
    </div>

    {{-- Net Profit: NEW --}}
    <div class="card">
        <p class="text-muted">Estimated Net Profit</p>
        <h2>KSh {{ number_format((float) $netProfit, 2) }}</h2>
        <p class="text-muted">Gross profit minus recorded expenses</p>
    </div>

</div>

{{-- Sales Transactions --}}
<div class="card" style="margin-top: 24px;">

    <h2>Sales Transactions</h2>

    <p class="text-muted">
        Period:
        {{ \Illuminate\Support\Carbon::parse($startDate)->format('d M Y') }}
        to
        {{ \Illuminate\Support\Carbon::parse($endDate)->format('d M Y') }}
    </p>

    @if($sales->isEmpty())

        <p class="text-muted">
            No completed sales were found for this period.
        </p>

    @else

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Receipt Number</th>
                        <th>Date</th>
                        <th>Cashier</th>
                        <th>Subtotal (KSh)</th>
                        <th>Discount (KSh)</th>
                        <th>Total (KSh)</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($sales as $sale)
                        <tr>
                            <td>{{ $sale->receipt_number }}</td>

                            <td>
                                {{ $sale->sold_at
                                    ? $sale->sold_at->format('d M Y, H:i')
                                    : '—' }}
                            </td>

                            <td>{{ $sale->user?->name ?? 'Unknown' }}</td>

                            <td>
                                {{ number_format((float) $sale->subtotal, 2) }}
                            </td>

                            <td>
                                {{ number_format((float) $sale->discount, 2) }}
                            </td>

                            <td>
                                {{ number_format((float) $sale->total_amount, 2) }}
                            </td>

                            <td>
                                <a href="{{ route('sales.receipt', $sale) }}"
                                   class="btn-secondary">
                                    View Receipt
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 20px;">
            {{ $sales->links() }}
        </div>

    @endif

</div>

@endsection
