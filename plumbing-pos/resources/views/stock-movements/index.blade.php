
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Movement History - Plumbing POS</title>

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

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        input, button, .link {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button, .link {
            background: #1769aa;
            color: white;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #e9eef5;
        }

        .type {
            text-transform: capitalize;
        }

        @media (max-width: 750px) {
            .table-wrapper {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="top">
        <div>
            <h1>Stock Movement History</h1>
            <p>Track changes in your plumbing inventory.</p>
        </div>

        <a class="link" href="{{ route('stock.index') }}">Back to Stock</a>
    </div>

    <form method="GET" action="{{ route('stock-movements.index') }}">
        <input
            type="text"
            name="search"
            placeholder="Search product, SKU, reference or type"
            value="{{ request('search') }}"
        >
        <button type="submit">Search</button>
        <a href="{{ route('stock-movements.index') }}">Clear</a>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Movement Type</th>
                    <th>Quantity</th>
                    <th>Reference</th>
                    <th>Recorded By</th>
                    <th>Notes</th>
                </tr>
            </thead>

            <tbody>
                @forelse($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at?->format('d M Y, H:i') }}</td>
                        <td>{{ $movement->product?->name ?? 'Product unavailable' }}</td>
                        <td>{{ $movement->product?->sku ?? 'N/A' }}</td>
                        <td class="type">{{ $movement->type }}</td>
                        <td>{{ number_format((float) $movement->quantity, 3) }}</td>
                        <td>{{ $movement->reference ?? 'N/A' }}</td>
                        <td>{{ $movement->user?->name ?? 'System / Not recorded' }}</td>
                        <td>{{ $movement->notes ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">No stock movements found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $movements->links() }}
    </div>
</div>

</body>
</html>