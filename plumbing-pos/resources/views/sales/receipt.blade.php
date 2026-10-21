@extends('layouts.app')

@section('title', 'Receipt ' . $sale->receipt_number)
@section('page-heading', 'Sales Receipt')
@section('page-description', 'View and print sale details')

@section('content')

@php
    $payment = $sale->payments->first();
@endphp

<div class="card receipt-container">

    <div class="receipt-header">
        <h2>PLUMBING SHOP</h2>
        <p>Sales Receipt</p>
        <h3>{{ $sale->receipt_number }}</h3>
    </div>

    <div class="receipt-details">
        <p>
            <strong>Date:</strong>
            {{ $sale->sold_at?->format('d M Y, h:i A') ?? $sale->created_at->format('d M Y, h:i A') }}
        </p>

        <p>
            <strong>Cashier:</strong>
            {{ $sale->user?->name ?? $sale->user?->email ?? 'Unknown' }}
        </p>

        <p>
            <strong>Payment Method:</strong>
            {{ strtoupper($payment?->method ?? 'N/A') }}
        </p>

        <p>
            <strong>Payment Status:</strong>
            {{ ucfirst($payment?->status ?? $sale->status) }}
        </p>

        @if($payment?->method === 'mpesa' && $payment->transaction_reference)
            <p>
                <strong>M-Pesa Reference:</strong>
                {{ $payment->transaction_reference }}
            </p>
        @endif
    </div>

    <hr>

    <h3>Items Purchased</h3>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Line Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($sale->items as $item)
                    <tr>
                        <td>{{ $item->product?->name ?? 'Product unavailable' }}</td>
                        <td>{{ number_format((float) $item->quantity, 3) }}</td>
                        <td>KSh {{ number_format((float) $item->unit_price, 2) }}</td>
                        <td>KSh {{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="receipt-total">
        <p>
            <span>Subtotal</span>
            <strong>KSh {{ number_format((float) $sale->subtotal, 2) }}</strong>
        </p>

        <p>
            <span>Discount</span>
            <strong>KSh {{ number_format((float) $sale->discount, 2) }}</strong>
        </p>

        <p class="grand-total">
            <span>Total</span>
            <strong>KSh {{ number_format((float) $sale->total_amount, 2) }}</strong>
        </p>
    </div>

    @if($payment?->status === 'pending' || $sale->status === 'pending')
        <div class="alert alert-error">
            Payment is pending verification. This receipt is not confirmation of payment.
        </div>
    @endif

    <div class="receipt-footer">
        <p>Thank you for shopping with us!</p>
    </div>

    <div class="receipt-actions no-print">
        <button type="button" onclick="window.print()">
            Print Receipt
        </button>

        <a href="{{ route('sales.index') }}" class="btn-secondary">
            Back to Sales
        </a>
    </div>

</div>

@endsection

@push('styles')
<style>
    .receipt-container {
        max-width: 950px;
        margin: 0 auto;
    }

    .receipt-header {
        text-align: center;
        padding-bottom: 18px;
        border-bottom: 2px solid var(--border);
    }

    .receipt-header h2 {
        color: var(--primary);
        margin-bottom: 5px;
    }

    .receipt-header h3 {
        margin-top: 12px;
    }

    .receipt-details {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 5px 20px;
        margin: 20px 0;
    }

    .receipt-details p {
        margin: 5px 0;
        overflow-wrap: anywhere;
    }

    .receipt-total {
        max-width: 380px;
        margin: 20px 0 20px auto;
    }

    .receipt-total p {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        padding: 8px 0;
        border-bottom: 1px solid var(--border);
    }

    .receipt-total .grand-total {
        font-size: 1.15rem;
        color: var(--primary);
        border-bottom: 2px solid var(--primary);
    }

    .receipt-footer {
        text-align: center;
        margin-top: 30px;
        font-weight: 600;
    }

    .receipt-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .btn-secondary {
        display: inline-block;
        padding: 10px 14px;
        border-radius: 6px;
        background: #e7edf4;
        color: var(--text);
        text-decoration: none;
    }

    @media (max-width: 600px) {
        .receipt-details {
            grid-template-columns: 1fr;
        }
    }

    @media print {
        body {
            background: #fff !important;
        }

        .sidebar,
        .topbar,
        .no-print {
            display: none !important;
        }

        .app-layout,
        .main-content,
        .page-content,
        .receipt-container {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
            background: #fff !important;
        }

        body,
        table {
            color: #000 !important;
            font-size: 11pt;
        }

        th,
        td {
            border: 1px solid #ccc !important;
        }

        @page {
            margin: 15mm;
        }
    }
</style>
@endpush