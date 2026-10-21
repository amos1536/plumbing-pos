
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receive Stock | Plumbing POS</title>

    <style>
        body {
            margin: 0;
            padding: 25px;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #263238;
        }

        .container {
            max-width: 700px;
            margin: 20px auto;
            background: white;
            padding: 28px;
            border-radius: 8px;
        }

        h1 {
            color: #17365d;
            margin-top: 0;
        }

        label {
            display: block;
            font-weight: bold;
            margin: 17px 0 7px;
        }

        input, select, textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        button, .back {
            display: inline-block;
            padding: 11px 16px;
            background: #17365d;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            margin-top: 22px;
        }

        .back {
            background: #64748b;
            margin-left: 8px;
        }

        .error {
            color: #b91c1c;
            font-size: 14px;
            margin-top: 5px;
        }

        .message {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Receive Stock</h1>
    <p>Record a new delivery from a supplier.</p>

    @if ($errors->any())
        <div class="message">
            <strong>Please correct the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($suppliers->isEmpty())
        <p>No active suppliers are registered. Add a supplier first.</p>
    @elseif ($products->isEmpty())
        <p>No active products are registered. Add a product first.</p>
    @else
        <form action="{{ route('stock.receive.store') }}" method="POST">
            @csrf

            <label for="supplier_id">Supplier *</label>
            <select id="supplier_id" name="supplier_id" required>
                <option value="">Select supplier</option>
                @foreach ($suppliers as $supplier)
                    <option
                        value="{{ $supplier->id }}"
                        @selected(old('supplier_id') == $supplier->id)
                    >
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>

            <label for="product_id">Product *</label>
            <select id="product_id" name="product_id" required>
                <option value="">Select product</option>
                @foreach ($products as $product)
                    <option
                        value="{{ $product->id }}"
                        @selected(old('product_id') == $product->id)
                    >
                        {{ $product->name }} ({{ $product->sku }})
                    </option>
                @endforeach
            </select>

            <label for="purchase_date">Purchase Date *</label>
            <input
                type="date"
                id="purchase_date"
                name="purchase_date"
                value="{{ old('purchase_date', now()->toDateString()) }}"
                required
            >

            <label for="quantity">Quantity Received *</label>
            <input
                type="number"
                id="quantity"
                name="quantity"
                min="0.001"
                step="0.001"
                value="{{ old('quantity') }}"
                required
            >

            <label for="unit_cost">Cost per Unit (KSh) *</label>
            <input
                type="number"
                id="unit_cost"
                name="unit_cost"
                min="0"
                step="0.01"
                value="{{ old('unit_cost') }}"
                required
            >

            <label for="notes">Notes</label>
            <textarea
                id="notes"
                name="notes"
                rows="3"
            >{{ old('notes') }}</textarea>

            <button type="submit">Receive Stock</button>
            <a href="{{ route('stock.index') }}" class="back">Cancel</a>
        </form>
    @endif
</div>

</body>
</html>