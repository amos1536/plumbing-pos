@extends('layouts.app')

@section('title', 'Process Return - Plumbing POS')
@section('page-heading', 'Process Sales Return')
@section('page-description', 'Record products returned by a customer')

@section('content')

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

<div class="card">
    <h2>Original Sale</h2>
    <p><strong>Receipt:</strong> {{ $sale->receipt_number }}</p>
    <p>
        <strong>Date:</strong>
        {{ $sale->sold_at?->format('d M Y, h:i A') ?? $sale->created_at->format('d M Y, h:i A') }}
    </p>
    <p><strong>Sale Status:</strong> {{ ucfirst($sale->status) }}</p>
</div>

<div class="card" style="margin-top: 20px;">
    <h2>Items Eligible for Return</h2>

    @foreach($sale->items as $item)
        @php
            $alreadyReturned = (float) $returnedQuantities->get($item->id, 0);
            $remaining = max(0, (float) $item->quantity - $alreadyReturned);
        @endphp

        <div style="padding: 18px 0; border-bottom: 1px solid var(--border);">
            <h3>{{ $item->product?->name ?? 'Product unavailable' }}</h3>

            <p>
                Original quantity: {{ number_format((float) $item->quantity, 3) }}
                {{ $item->product?->unit }}
            </p>

            <p>
                Previously returned: {{ number_format($alreadyReturned, 3) }}
            </p>

            <p>
                Eligible quantity remaining: {{ number_format($remaining, 3) }}
            </p>

            <p>
                Original unit price:
                KSh {{ number_format((float) $item->unit_price, 2) }}
            </p>

            @if($remaining > 0 && $sale->status === 'completed')
                <form
                    action="{{ route('sales-returns.store', $sale) }}"
                    method="POST"
                    style="max-width: 550px;"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="sale_item_id"
                        value="{{ $item->id }}"
                    >

                    <label for="quantity-{{ $item->id }}">Quantity to return</label>
                    <input
                        id="quantity-{{ $item->id }}"
                        type="number"
                        name="quantity"
                        min="0.001"
                        max="{{ $remaining }}"
                        step="0.001"
                        value="{{ old('sale_item_id') == $item->id ? old('quantity') : '' }}"
                        required
                    >

                    <label for="reason-{{ $item->id }}">Reason for return</label>
                    <textarea
                        id="reason-{{ $item->id }}"
                        name="reason"
                        rows="2"
                        maxlength="1000"
                        required
                    >{{ old('sale_item_id') == $item->id ? old('reason') : '' }}</textarea>

                    <button
                        type="submit"
                        onclick="return confirm('Complete this return and add the quantity back to stock?')"
                    >
                        Record Return
                    </button>
                </form>
            @elseif($sale->status !== 'completed')
                <p class="text-muted">
                    Returns are available only for completed sales.
                </p>
            @else
                <p class="text-muted">
                    This item has no remaining quantity eligible for return.
                </p>
            @endif
        </div>
    @endforeach
</div>

<div style="margin-top: 20px;">
    <a href="{{ route('sales.index') }}" class="btn-secondary">
        Back to Sales
    </a>
</div>

@endsection