
@extends('layouts.app')

@section('title', 'Draft Sales - Plumbing POS')
@section('page-heading', 'Draft Sales')
@section('page-description', 'Saved sales waiting to be resumed')

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <h2>Saved Drafts</h2>

    @if($drafts->isEmpty())
        <p class="text-muted">There are no saved drafts.</p>
    @else
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Draft Number</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Saved By</th>
                        <th>Date Saved</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($drafts as $draft)
                        <tr>
                            <td>{{ $draft->receipt_number }}</td>
                            <td>{{ $draft->items->count() }}</td>
                            <td>
                                KSh {{ number_format((float) $draft->total_amount, 2) }}
                            </td>
                            <td>{{ $draft->user->name ?? 'Unknown' }}</td>
                            <td>{{ $draft->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <a
                                    class="btn-primary"
                                    href="{{ route('sales.drafts.resume', $draft) }}"
                                >
                                    Resume
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $drafts->links() }}
    @endif

    <p style="margin-top:20px;">
        <a href="{{ route('sales.create') }}">Back to New Sale</a>
    </p>
</div>

@endsection
