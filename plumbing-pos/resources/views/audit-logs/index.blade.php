@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Audit Logs</h1>
            <p class="text-muted mb-0">
                Review recorded activities performed in the POS.
            </p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('audit-logs.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-7">
                        <label for="search" class="form-label">
                            Search activity
                        </label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Search action, user, entity or description"
                        >
                    </div>

                    <div class="col-md-3">
                        <label for="action" class="form-label">
                            Action
                        </label>
                        <select
                            id="action"
                            name="action"
                            class="form-select"
                        >
                            <option value="">All actions</option>

                            @foreach ($actions as $availableAction)
                                <option
                                    value="{{ $availableAction }}"
                                    @selected(request('action') === $availableAction)
                                >
                                    {{ ucwords(str_replace('_', ' ', $availableAction)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Search
                        </button>

                        <a
                            href="{{ route('audit-logs.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Date &amp; Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($auditLogs as $log)
                            <tr>
                                <td class="text-nowrap">
                                    {{ $log->created_at?->format('d M Y, H:i:s') ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $log->user?->name ?? 'Unknown user' }}
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>

                                <td>
                                    {{ ucwords(str_replace('_', ' ', $log->entity_type)) }}
                                    @if ($log->entity_id)
                                        #{{ $log->entity_id }}
                                    @endif
                                </td>

                                <td style="min-width: 280px; white-space: normal;">
                                    {{ $log->description }}
                                </td>

                                <td>{{ $log->ip_address ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No audit logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $auditLogs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
