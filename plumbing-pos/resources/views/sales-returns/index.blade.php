@extends('layouts.app')

@section('title', 'Sales Returns - Plumbing POS')
@section('page-heading', 'Sales Returns')
@section('page-description', 'View recorded product returns')

@section('content')
<div class="card">
    <h2>Return History</h2>

    @if($returns->isEmpty())
        <p class="text-muted">No returns have been recorded yet.</p>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Return ID</th>
                        <th>Receipt Number</th>
                        <th>Quantity</th>
                        <th>Refund Amount</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Recorded By</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returns as $return)
                        <tr>
                            <td>{{ $return->id }}</td>
                            <td>{{ $return->receipt_number }}</td>
                            <td>{{ number_format((float) $return->quantity, 3) }}</td>
                            <td>
                                KSh {{ number_format((float) $return->refund_amount, 2) }}
                            </td>
                            <td>{{ $return->reason }}</td>
                            <td>{{ ucfirst($return->status) }}</td>
                            <td>{{ $return->cashier_name ?? 'Unknown' }}</td>
                            <td>
                                {{ \Illuminate\Support\Carbon::parse($return->created_at)->format('d M Y, h:i A') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $returns->links() }}
        </div>
    @endif
</div>
@endsection