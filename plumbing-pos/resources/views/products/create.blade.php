
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Product - Plumbing POS</title>
    <style>
        body {
             font-family: Arial, sans-serif; 
             margin: 30px; 
             background: #f4f6f9; 
            }
        .container { 
            max-width: 800px; 
            margin: auto; 
            background: white; 
            padding: 25px; 
            border-radius: 8px; 
        }
        .grid {
             display: grid; 
             grid-template-columns: 1fr 1fr; 
             gap: 16px; 
            }
        label {
             display: block; 
             margin-bottom: 6px; 
             font-weight: bold; 
            }
        input, select {
             width: 100%; 
             box-sizing: border-box; 
             padding: 10px; 
             margin-bottom: 15px; 
            }
        .btn {
             background: #176b45; 
             color: white; 
             padding: 11px 16px; 
             border: 0; 
             border-radius: 5px; 
             cursor: pointer; 
            }
        .error {
             color: #b42318; 
             margin-bottom: 12px; 
            }
        @media (max-width: 600px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="container">
    <h1>Add Plumbing Product</h1>

    @if($errors->any())
        <div class="error">
            <strong>Please correct these errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($categories->isEmpty())
        <p>You need to register a product category before adding products.</p>
        <a href="{{ route('categories.create') }}">Add Category</a>
    @else
        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="grid">
                <div>
                    <label for="name">Product Name *</label>
                    <input id="name" name="name" value="{{ old('name') }}" required>
                </div>

                <div>
                    <label for="sku">SKU / Product Code *</label>
                    <input id="sku" name="sku" value="{{ old('sku') }}" required>
                </div>

                <div>
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                @selected(old('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="barcode">Barcode</label>
                    <input id="barcode" name="barcode" value="{{ old('barcode') }}">
                </div>

                <div>
                    <label for="brand">Brand</label>
                    <input id="brand" name="brand" value="{{ old('brand') }}">
                </div>

                <div>
                    <label for="size">Size / Specification</label>
                    <input id="size" name="size" value="{{ old('size') }}"
                           placeholder="e.g. 20mm, 1/2 inch">
                </div>

                <div>
                    <label for="unit">Unit *</label>
                    <select id="unit" name="unit" required>
                        @foreach(['piece', 'metre', 'roll', 'packet', 'box', 'set'] as $unit)
                            <option value="{{ $unit }}"
                                @selected(old('unit', 'piece') == $unit)>
                                {{ ucfirst($unit) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="cost_price">Cost Price (KSh) *</label>
                    <input type="number" id="cost_price" name="cost_price"
                           min="0" step="0.01" value="{{ old('cost_price') }}" required>
                </div>

                <div>
                    <label for="selling_price">Selling Price (KSh) *</label>
                    <input type="number" id="selling_price" name="selling_price"
                           min="0" step="0.01" value="{{ old('selling_price') }}" required>
                </div>

                <div>
                    <label for="quantity_on_hand">Opening Stock *</label>
                    <input type="number" id="quantity_on_hand" name="quantity_on_hand"
                           min="0" step="0.001" value="{{ old('quantity_on_hand', 0) }}" required>
                </div>

                <div>
                    <label for="reorder_level">Low Stock Alert Level *</label>
                    <input type="number" id="reorder_level" name="reorder_level"
                           min="0" step="0.001" value="{{ old('reorder_level', 5) }}" required>
                </div>
            </div>

            <button class="btn" type="submit">Save Product</button>
            <a href="{{ route('products.index') }}">Cancel</a>
        </form>
    @endif
</div>
</body>
</html>