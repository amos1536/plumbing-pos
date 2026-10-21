
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers - Plumbing POS</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f4f6f9;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        a, button {
            display: inline-block;
            padding: 9px 13px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            color: white;
            background: #1769aa;
        }

        .danger {
            background: #c62828;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #e9eef5;
        }

        .success {
            padding: 12px;
            background: #d4edda;
            margin-bottom: 15px;
        }

        .inactive {
            color: #c62828;
        }

        .active {
            color: #16803c;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Supplier Management</h1>
    <p>Register and manage suppliers for your plumbing shop.</p>

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    <div class="actions">
        <a href="{{ route('suppliers.create') }}">Add Supplier</a>
        <a href="{{ route('stock.receive.create') }}">Receive Stock</a>
        <a href="{{ route('products.index') }}">Products</a>
    </div>

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
            @forelse($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->phone ?? 'N/A' }}</td>
                    <td>{{ $supplier->email ?? 'N/A' }}</td>
                    <td>{{ $supplier->address ?? 'N/A' }}</td>
                    <td>{{ $supplier->kra_pin ?? 'N/A' }}</td>
                    <td>{{ $supplier->purchases_count }}</td>

                    <td>
                        @if($supplier->is_active)
                            <span class="active">Active</span>
                        @else
                            <span class="inactive">Inactive</span>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('suppliers.edit', $supplier) }}">
                            Edit
                        </a>

                        @if($supplier->is_active)
                            <form action="{{ route('suppliers.destroy', $supplier) }}"
                                  method="POST"
                                  style="display:inline;"
                                  onsubmit="return confirm('Deactivate this supplier?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="danger">
                                    Deactivate
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No suppliers registered yet. Click Add Supplier to register one.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $suppliers->links() }}
    </div>
</div>

</body>
</html>