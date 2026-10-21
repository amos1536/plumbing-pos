<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management | Plumbing POS</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #263238;
        }

        .header {
            background: #17365d;
            color: white;
            padding: 20px 30px;
        }

        .header h1 {
            margin: 0 0 6px;
        }

        .header p {
            margin: 0;
            color: #dce6f1;
        }

        .container {
            max-width: 1250px;
            margin: 25px auto;
            padding: 0 20px;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 8px;
            box-shadow: 0 2px 8px #0000000d;
        }

        .card h3 {
            margin: 0 0 12px;
            color: #596579;
            font-size: 15px;
        }

        .number {
            font-size: 28px;
            font-weight: bold;
        }

        .warning {
            color: #b45309;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .search-form {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        input {
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            min-width: 230px;
        }

        button, .button {
            padding: 10px 14px;
            border: none;
            border-radius: 5px;
            background: #17365d;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .table-wrapper {
            overflow-x: auto;
            background: white;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th, td {
            text-align: left;
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: #eef2f7;
            font-size: 13px;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .in-stock {
            color: #166534;
            background: #dcfce7;
        }

        .low-stock {
            color: #92400e;
            background: #fef3c7;
        }

        .out-stock {
            color: #991b1b;
            background: #fee2e2;
        }

        .pagination {
            padding: 18px;
        }

        .empty {
            padding: 30px;
            text-align: center;
            color: #64748b;
        }

        @media (max-width: 600px) {
            .summary {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 18px;
            }
        }
    </style>
</head>
<body>

<header class="header">
    <h1>Stock Management</h1>
    <p>Plumbing Shop Point of Sale System</p>
</header>

<main class="container">

    <section class="summary">
        <div class="card">
            <h3>Active Products</h3>
            <div class="number">{{ $totalProducts }}</div>
        </div>

        <div class="card">
            <h3>Products Requiring Restocking</h3>
            <div class="number warning">{{ $lowStockCount }}</div>
        </div>
    </section>

    <section class="card">
        <div class="toolbar">
            <h2>Inventory List</h2>

            <form action="{{ route('stock.index') }}"
                  method="GET"
                  class="search-form">
                <input
                    type="text"
                    name="search"
                    placeholder="Search name, SKU or barcode"
                    value="{{ $search }}"
                >

                <button type="submit">Search</button>

                <a href="{{ route('stock.index') }}"
                   class="button">Reset</a>
            </form>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Quantity Available</th>
                        <th>Reorder Level</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->name }}</td>
                            <td>
                                {{ $product->category?->name ?? 'Uncategorized' }}
                            </td>
                            <td>{{ $product->unit }}</td>
                            <td>{{ $product->quantity_on_hand }}</td>
                            <td>{{ $product->reorder_level }}</td>
                            <td>
                                @if ((float) $product->quantity_on_hand <= 0)
                                    <span class="status out-stock">
                                        Out of Stock
                                    </span>
                                @elseif ((float) $product->quantity_on_hand <= (float) $product->reorder_level)
                                    <span class="status low-stock">
                                        Low Stock
                                    </span>
                                @else
                                    <span class="status in-stock">
                                        In Stock
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">
                                No products found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $products->links() }}
        </div>
    </section>

</main>

</body>
</html>