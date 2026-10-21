 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products - Plumbing POS</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 28px;
            background: #f5f6fa;
            color: #202334;
        }

        .container {
            max-width: 1450px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .header h1 {
            margin: 0 0 7px;
            font-size: 26px;
        }

        .muted {
            color: #7b8190;
            font-size: 13px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            background: #3026a4;
            color: white;
            padding: 11px 16px;
            text-decoration: none;
            border: none;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn:hover {
            background: #241b83;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 13px 16px;
            border-radius: 9px;
            margin-bottom: 18px;
        }

        .error {
            color: #b42318;
            background: #fee2e2;
            padding: 12px 18px;
            border-radius: 9px;
            margin-bottom: 18px;
        }

        .toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            flex-wrap: wrap;
        }

        .search-form input,
        .search-form select {
            min-width: 0;
            padding: 12px 14px;
            border: 1px solid #e0e3eb;
            border-radius: 9px;
            background: white;
            color: #202334;
            font-size: 14px;
            height: 43px;
        }

        .search-form input {
            flex: 1;
            min-width: 220px;
        }

        .search-form select {
            width: 220px;
            cursor: pointer;
        }

        .search-form input:focus,
        .search-form select:focus {
            outline: 2px solid #c7d2fe;
            border-color: #6366f1;
        }

        .clear-btn {
            background: #e5e7eb;
            color: #374151;
        }

        .clear-btn:hover {
            background: #d1d5db;
        }

        .categories {
            display: flex;
            gap: 9px;
            flex-wrap: wrap;
            margin-bottom: 23px;
        }

        .category {
            display: inline-block;
            padding: 9px 16px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            color: #4b5563;
            text-decoration: none;
            font-size: 13px;
            white-space: nowrap;
        }

        .category:hover {
            border-color: #3026a4;
            color: #3026a4;
        }

        .category.active {
            background: #3026a4;
            color: white;
            border-color: #3026a4;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .product-card {
            min-width: 0;
            background: white;
            border: 1px solid #e8eaf0;
            border-radius: 15px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: box-shadow 0.2s, transform 0.2s;
        }

        .product-card:hover {
            box-shadow: 0 7px 22px rgba(30, 35, 70, 0.08);
            transform: translateY(-2px);
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
        }

        .product-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #eef0ff;
            color: #3026a4;
            font-size: 27px;
            flex-shrink: 0;
        }

        .stock {
            background: #dcfce7;
            color: #15803d;
            padding: 5px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
        }

        .stock.low {
            background: #fef3c7;
            color: #92400e;
        }

        .stock.out {
            background: #fee2e2;
            color: #b91c1c;
        }

        .product-name {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .product-details {
            color: #7b8190;
            font-size: 12px;
            line-height: 1.7;
        }

        .product-category {
            color: #6366a1;
            font-size: 12px;
        }

        .price {
            font-size: 18px;
            font-weight: 700;
            color: #202334;
            margin-top: auto;
        }

        .card-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 2px;
        }

        .action-link {
            display: inline-block;
            flex: 1;
            text-align: center;
            text-decoration: none;
            padding: 9px 8px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
        }

        .edit-link {
            background: #eef0ff;
            color: #3026a4;
        }

        .deactivate-button {
            background: #fff1f2;
            color: #be123c;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .empty {
            background: white;
            border: 1px dashed #d1d5db;
            padding: 45px 20px;
            border-radius: 14px;
            text-align: center;
            color: #6b7280;
            grid-column: 1 / -1;
        }

        .pagination {
            margin-top: 25px;
        }

        @media (max-width: 1100px) {
            .products-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            body {
                padding: 16px;
            }

            .products-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .product-card {
                padding: 12px;
            }

            .header h1 {
                font-size: 22px;
            }

            .search-form input {
                min-width: 100%;
            }

            .search-form select {
                width: 100%;
            }
        }

        @media (max-width: 420px) {
            .products-grid {
                grid-template-columns: 1fr;
            }

            .search-form .btn {
                flex: 1;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <div>
            <h1>Products</h1>
            <span class="muted">
                {{ $products->total() }} products found
            </span>
        </div>

        <a class="btn" href="{{ route('products.create') }}">
            <span>+</span> Add Product
        </a>
    </div>

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="toolbar">
        <form class="search-form"
              action="{{ route('products.index') }}"
              method="GET">

            <input
                type="search"
                name="search"
                placeholder="Search product name or SKU..."
                value="{{ request('search') }}"
            >

            <select name="category_id">
                <option value="">All Categories</option>

                @foreach($categories as $category)
                    <option
                        value="{{ $category->id }}"
                        {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <button class="btn" type="submit">Search</button>

            <a class="btn clear-btn"
               href="{{ route('products.index') }}">
                Clear
            </a>
        </form>
    </div>

    <div class="categories">
        <a
            class="category {{ !request('category_id') ? 'active' : '' }}"
            href="{{ route('products.index', request()->except('category_id', 'page')) }}"
        >
            All
        </a>

        @foreach($categories as $category)
            <a
                class="category {{ (string) request('category_id') === (string) $category->id ? 'active' : '' }}"
                href="{{ route('products.index', array_merge(request()->except('page'), ['category_id' => $category->id])) }}"
            >
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    <div class="products-grid">

        @forelse($products as $product)

            @php
                $stock = (float) $product->quantity_on_hand;
                $reorder = (float) $product->reorder_level;

                $icon = '🔧';

                $categoryName = strtolower(
                    $product->category?->name ?? ''
                );

                if (str_contains($categoryName, 'pipe')) {
                    $icon = '🪠';
                } elseif (str_contains($categoryName, 'valve')) {
                    $icon = '⚙️';
                } elseif (
                    str_contains($categoryName, 'tap') ||
                    str_contains($categoryName, 'faucet')
                ) {
                    $icon = '🚰';
                } elseif (str_contains($categoryName, 'tool')) {
                    $icon = '🔧';
                } elseif (str_contains($categoryName, 'sanitary')) {
                    $icon = '🚿';
                }

                $stockClass = $stock <= 0
                    ? 'out'
                    : ($stock <= $reorder ? 'low' : '');
            @endphp

            <div class="product-card">

                <div class="card-top">
                    <div class="product-icon">
                        {{ $icon }}
                    </div>

                    <span class="stock {{ $stockClass }}">
                        @if($stock <= 0)
                            Out of stock
                        @else
                            {{ rtrim(rtrim(number_format($stock, 3, '.', ''), '0'), '.') }}
                            in stock
                        @endif
                    </span>
                </div>

                <div class="product-name">
                    {{ $product->name }}
                </div>

                <div class="product-details">
                    SKU: {{ $product->sku }}

                    @if($product->brand)
                        <br>Brand: {{ $product->brand }}
                    @endif

                    @if($product->size)
                        <br>Size: {{ $product->size }}
                    @endif
                </div>

                <div class="product-category">
                    {{ $product->category?->name ?? 'Uncategorised' }}
                </div>

                <div class="price">
                    KES {{ number_format((float) $product->selling_price, 2) }}
                </div>

                <div class="product-details">
                    Unit: {{ $product->unit }}
                    <br>
                    Status:
                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                </div>

                @if($product->is_active)
                    <div class="card-actions">

                        <a
                            class="action-link edit-link"
                            href="{{ route('products.edit', $product) }}"
                        >
                            Edit
                        </a>

                        <form
                            action="{{ route('products.destroy', $product) }}"
                            method="POST"
                            onsubmit="return confirm('Deactivate this product?')"
                            style="flex: 1;"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                class="action-link deactivate-button"
                                type="submit"
                                style="width: 100%;"
                            >
                                Deactivate
                            </button>
                        </form>

                    </div>
                @else
                    <div class="muted">
                        This product is inactive.
                    </div>
                @endif

            </div>

        @empty

            <div class="empty">
                <div style="font-size: 35px; margin-bottom: 10px;">
                    📦
                </div>

                <strong>No products found</strong>

                <p>
                    Try another search or category, or register a new product.
                </p>

                <a class="btn" href="{{ route('products.create') }}">
                    + Add Product
                </a>
            </div>

        @endforelse

    </div>

    <div class="pagination">
        {{ $products->links() }}
    </div>

</div>
</body>
</html>
