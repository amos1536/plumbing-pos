
@extends('layouts.app')

@section('title', 'Sales History - Plumbing POS')
@section('page-heading', 'Sales History')
@section('page-description', 'View and manage completed sales')

@section('content')

<style>
    .sales-container {
        width: 100%;
    }

    .sales-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }

    .sales-header h2 {
        margin: 0 0 6px;
    }

    .sales-header p {
        margin: 0;
    }

    .sales-count {
        margin-bottom: 18px;
        color: #64748b;
        font-size: 14px;
    }

    .sales-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 18px;
    }

    .sale-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        transition: transform 0.2s, box-shadow 0.2s;
        min-width: 0;
    }

    .sale-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    }

    .sale-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 18px;
    }

    .sale-receipt {
        font-size: 16px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .sale-date {
        font-size: 12px;
        color: #64748b;
        margin-top: 5px;
    }

    .sale-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        background: #f1f5f9;
        color: #475569;
    }

    .sale-status-completed {
        background: #dcfce7;
        color: #166534;
    }

    .sale-status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .sale-status-cancelled,
    .sale-status-refunded {
        background: #fee2e2;
        color: #991b1b;
    }

    .sale-details {
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 0;
        margin-bottom: 16px;
    }

    .sale-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .sale-detail-row:last-child {
        margin-bottom: 0;
    }

    .sale-detail-label {
        color: #64748b;
    }

    .sale-detail-value {
        text-align: right;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .sale-total-label {
        color: #475569;
        font-size: 14px;
    }

    .sale-total {
        font-size: 22px;
        font-weight: 700;
        color: #0f766e;
        margin-top: 4px;
    }

    .sale-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .sale-actions a {
        text-decoration: none;
        text-align: center;
        flex: 1;
        min-width: 110px;
        padding: 9px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        box-sizing: border-box;
    }

    .sale-btn-view {
        background: #0f766e;
        color: #fff;
    }

    .sale-btn-view:hover {
        background: #115e59;
    }

    .sale-btn-return {
        background: #fff;
        color: #334155;
        border: 1px solid #cbd5e1;
    }

    .sale-btn-return:hover {
        background: #f1f5f9;
    }

    .sales-empty {
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 45px 20px;
        text-align: center;
        color: #64748b;
    }

    .sales-empty h3 {
        color: #334155;
        margin-top: 0;
    }

    .sales-pagination {
        margin-top: 24px;
    }

    @media (max-width: 480px) {
        .sales-grid {
            grid-template-columns: 1fr;
        }

        .sale-card {
            padding: 16px;
        }

        .sale-card-top {
            flex-wrap: wrap;
        }
    }
</style>

<div class="sales-container">

    <div class="sales-header">
        <div>
            <h2>All Sales</h2>
            <p class="text-muted">
                Review transactions recorded in your shop.
            </p>
        </div>

        <a href="{{ route('sales.create') }}" class="btn">
            + New Sale
        </a>
    </div>

    <div class="sales-count">
        {{ $sales->total() }} sales found
    </div>

    @if($sales->count())

        <div class="sales-grid">

            @foreach($sales as $sale)

                @php
                    $statusClass = 'sale-status-' . strtolower($sale->status);
                @endphp

                <div class="sale-card">

                    <div class="sale-card-top">
                        <div>
                            <div class="sale-receipt">
                                {{ $sale->receipt_number }}
                            </div>

                            <div class="sale-date">
                                {{ $sale->sold_at
                                    ? $sale->sold_at->format('d M Y, H:i')
                                    : $sale->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>

                        <span class="sale-status {{ $statusClass }}">
                            {{ ucfirst($sale->status) }}
                        </span>
                    </div>

                    <div class="sale-details">

                        <div class="sale-detail-row">
                            <span class="sale-detail-label">Cashier</span>

                            <span class="sale-detail-value">
                                {{ $sale->user?->name ?? 'Unknown' }}
                            </span>
                        </div>

                        <div class="sale-detail-row">
                            <span class="sale-detail-label">Receipt Number</span>

                            <span class="sale-detail-value">
                                {{ $sale->receipt_number }}
                            </span>
                        </div>

                    </div>

                    <div class="sale-total-label">
                        Total Amount
                    </div>

                    <div class="sale-total">
                        KSh {{ number_format((float) $sale->total_amount, 2) }}
                    </div>

                    <div class="sale-actions">

                        <a
                            href="{{ route('sales.receipt', $sale) }}"
                            class="sale-btn-view"
                        >
                            View Receipt
                        </a>

                        @if($sale->status === 'completed')
                            <a
                                href="{{ route('sales-returns.create', $sale) }}"
                                class="sale-btn-return"
                            >
                                Process Return
                            </a>
                        @endif

                    </div>

                </div>

            @endforeach

        </div>

        <div class="sales-pagination">
            {{ $sales->links() }}
        </div>

    @else

        <div class="sales-empty">
            <h3>No Sales Found</h3>

            <p>
                No sales have been recorded yet. Create a new sale to see it here.
            </p>

            <a href="{{ route('sales.create') }}" class="btn">
                + Create First Sale
            </a>
        </div>

    @endif

</div>

@endsection
