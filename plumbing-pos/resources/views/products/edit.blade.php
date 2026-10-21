
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Product</h1>

    @if ($errors->any())
        <div style="background:#fee2e2;color:#991b1b;padding:12px;margin-bottom:15px;border-radius:6px;">
            <strong>Please correct the following errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product->id) }}"
          method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom:15px;">
            <label for="category_id">Category</label><br>
            <select name="category_id" id="category_id" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label for="sku">SKU</label><br>
            <input type="text" name="sku" id="sku"
                   value="{{ old('sku', $product->sku) }}" required>
        </div>

        <div style="margin-bottom:15px;">
            <label for="barcode">Barcode</label><br>
            <input type="text" name="barcode" id="barcode"
                   value="{{ old('barcode', $product->barcode) }}">
        </div>

        <div style="margin-bottom:15px;">
            <label for="name">Product Name</label><br>
            <input type="text" name="name" id="name"
                   value="{{ old('name', $product->name) }}" required>
        </div>

        <div style="margin-bottom:15px;">
            <label for="brand">Brand</label><br>
            <input type="text" name="brand" id="brand"
                   value="{{ old('brand', $product->brand) }}">
        </div>

        <div style="margin-bottom:15px;">
            <label for="size">Size</label><br>
            <input type="text" name="size" id="size"
                   value="{{ old('size', $product->size) }}">
        </div>

        <div style="margin-bottom:15px;">
            <label for="unit">Unit</label><br>
            <input type="text" name="unit" id="unit"
                   value="{{ old('unit', $product->unit) }}" required>
        </div>

        <div style="margin-bottom:15px;">
            <label for="cost_price">Cost Price</label><br>
            <input type="number" name="cost_price" id="cost_price"
                   value="{{ old('cost_price', $product->cost_price) }}"
                   min="0" step="0.01" required>
        </div>

        <div style="margin-bottom:15px;">
            <label for="selling_price">Selling Price</label><br>
            <input type="number" name="selling_price" id="selling_price"
                   value="{{ old('selling_price', $product->selling_price) }}"
                   min="0" step="0.01" required>
        </div>

        <div style="margin-bottom:15px;">
            <label for="reorder_level">Reorder Level</label><br>
            <input type="number" name="reorder_level" id="reorder_level"
                   value="{{ old('reorder_level', $product->reorder_level) }}"
                   min="0" step="0.001" required>
        </div>

        <div style="background:#eff6ff;padding:12px;margin-bottom:15px;border-radius:6px;">
            <strong>Current Stock:</strong>
            {{ $product->quantity_on_hand }} {{ $product->unit }}

            <p style="margin-bottom:0;">
                Stock quantity is managed through stock operations,
                not this form.
            </p>
        </div>

        <button type="submit"
                style="background:#2563eb;color:white;padding:10px 16px;border:0;border-radius:5px;cursor:pointer;">
            Update Product
        </button>

        <a href="{{ route('products.index') }}"
           style="margin-left:10px;">
            Cancel
        </a>
    </form>
</div>
@endsection