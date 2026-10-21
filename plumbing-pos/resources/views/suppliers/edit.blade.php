
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers | Plumbing POS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h1 { color: #1e3a5f; }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 5px;
            background: #1e3a5f;
            color: white;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .success {
            background: #e2f7e9;
            color: #176534;
            padding: 12px;
            border-radius: 5px;
        }

        .muted { color: #777; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th { background: #edf1f5; }

        .danger {
            background: #a52a2a;
        }

        @media (max-width: 700px) {
            .container { padding: 12px; overflow-x: auto; }
            table { min-width: 750px; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Supplier Management</h1>
    <p>Manage suppliers for your plumbing shop.</p>

    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <div class="actions">
        <a class="btn" href="{{ route('suppliers.create') }}">
            + Add Supplier
        </a>

        <a class="btn" href="{{ route('stock.receive.create') }}">
            Receive Stock
        </a>
    </div>

    @if ($suppliers->count())
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>KRA PIN</th>
                    <th>Purchases</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->phone ?: '—' }}</td>
                        <td>{{ $supplier->email ?: '—' }}</td>
                        <td>{{ $supplier->address ?: '—' }}</td>
                        <td>{{ $supplier->kra_pin ?: '—' }}</td>
                        <td>{{ $supplier->purchases_count }}</td>
                        <td>
                            {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                        </td>
                        <td>
                            <a href="{{ route('suppliers.edit', $supplier) }}">
                                Edit
                            </a>

                            @if ($supplier->is_active)
                                <form
                                    action="{{ route('suppliers.destroy', $supplier) }}"
                                    method="POST"
                                    style="display:inline"
                                    onsubmit="return confirm('Deactivate this supplier?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn danger" type="submit">
                                        Deactivate
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $suppliers->links() }}
    @else
        <p class="muted">
            No suppliers registered yet. Select Add Supplier to get started.
        </p>
    @endif
</div>
</body>
</html>