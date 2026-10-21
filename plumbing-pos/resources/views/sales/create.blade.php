
@extends('layouts.app')

@section('title', 'New Sale - Plumbing POS')
@section('page-heading', 'New Sale')
@section('page-description', 'Select products and process checkout')

@section('content')

<style>
    .checkout-products { margin-bottom: 24px; }

    .shopping-cart-topbar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 22px;
    }

    .shopping-cart-top-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 18px;
        background: #0f766e;
        color: #fff;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 3px 8px rgba(0,0,0,.12);
        transition: background .2s, transform .2s;
    }

    .shopping-cart-top-link:hover {
        background: #115e59;
        color: #fff;
        transform: translateY(-2px);
    }

    .shopping-cart-icon { width: 26px; height: 26px; flex-shrink: 0; }

    .shopping-cart-badge {
        position: absolute;
        top: -9px;
        right: -9px;
        min-width: 24px;
        height: 24px;
        padding: 0 5px;
        box-sizing: border-box;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #dc2626;
        color: #fff;
        border: 2px solid #fff;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
    }

    #shopping-cart { scroll-margin-top: 24px; }

    .products-toolbar {
        display: grid;
        grid-template-columns: minmax(220px, 2fr) minmax(150px, 1fr)
            minmax(160px, 1fr) auto;
        gap: 12px;
        margin: 20px 0;
        align-items: end;
    }

    .toolbar-field label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #475569;
    }

    .toolbar-field input, .toolbar-field select {
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        background: #fff;
        font: inherit;
    }

    .products-summary {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
        color: #64748b;
        font-size: 14px;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(235px, 1fr));
        gap: 18px;
    }

    .checkout-product-card {
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px;
        min-width: 0;
        box-shadow: 0 2px 6px rgba(0,0,0,.04);
        transition: transform .2s, box-shadow .2s;
    }

    .checkout-product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,.08);
    }

    .checkout-product-card[hidden], [hidden] {
        display: none !important;
    }

    .product-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 12px;
    }

    .product-card-name {
        font-size: 16px;
        font-weight: 700;
        line-height: 1.4;
        overflow-wrap: anywhere;
        color: #1e293b;
    }

    .product-card-sku {
        font-size: 12px;
        color: #64748b;
        margin-top: 5px;
        overflow-wrap: anywhere;
    }

    .product-stock-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 5px 8px;
        border-radius: 20px;
        background: #dcfce7;
        color: #166534;
        white-space: nowrap;
    }

    .product-stock-badge.low {
        background: #fef3c7;
        color: #92400e;
    }

    .product-card-details {
        border-top: 1px solid #e2e8f0;
        padding-top: 12px;
        margin-top: 4px;
        flex: 1;
    }

    .product-card-detail {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
        font-size: 13px;
    }

    .product-card-detail:last-child { margin-bottom: 0; }
    .product-card-label { color: #64748b; }

    .product-card-value {
        text-align: right;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .product-card-price {
        color: #0f766e;
        font-size: 21px;
        font-weight: 700;
        margin: 16px 0;
    }

    .product-add-button {
        width: 100%;
        padding: 10px 12px;
        border: none;
        border-radius: 7px;
        background: #0f766e;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .product-add-button:hover { background: #115e59; }

    .product-add-button:disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }

    .products-empty {
        text-align: center;
        padding: 35px 20px;
        color: #64748b;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        grid-column: 1 / -1;
    }

    .cart-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cart-line {
        display: flex;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        padding: 14px 0;
        border-bottom: 1px solid #dfe5ec;
    }

    .cart-line-description { flex: 1; min-width: 150px; }
    .cart-line-name { font-weight: 600; overflow-wrap: anywhere; }

    .cart-line-subtotal {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }

    .cart-line input {
        width: 100px;
        box-sizing: border-box;
        padding: 8px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
    }

    .cart-remove-button {
        padding: 8px 10px;
        border: 1px solid #fecaca;
        border-radius: 6px;
        background: #ee0808;
        color: #fff;
        cursor: pointer;
    }

    .cart-remove-button:hover { opacity: .8; }

    .cart-total {
        font-size: 23px;
        font-weight: 700;
        color: #0f766e;
        margin: 20px 0;
    }

    .checkout-payment-field {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .checkout-payment-select, .checkout-phone-input {
        width: 100%;
        max-width: 360px;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        background: #fff;
        font: inherit;
    }

    .checkout-phone-input:read-only {
        background: #f1f5f9;
        color: #475569;
    }

    .checkout-submit { margin-top: 18px; }

    .checkout-submit:disabled {
        opacity: .65;
        cursor: not-allowed;
    }

    .draft-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        margin-top: 10px;
    }

    .draft-notice {
        padding: 14px 16px;
        margin-bottom: 20px;
        background: #ecfeff;
        border: 1px solid #a5f3fc;
        border-radius: 8px;
        color: #155e75;
    }

    .cart-message {
        margin: 12px 0;
        padding: 10px 12px;
        border-radius: 7px;
        background: #fee2e2;
        color: #991b1b;
    }

    .payment-summary {
        margin-top: 16px;
        padding: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
    }

    @media (max-width: 850px) {
        .products-toolbar {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 520px) {
        .products-toolbar { grid-template-columns: 1fr; }
        .products-grid { grid-template-columns: 1fr; }
    }
</style>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-error">
        <strong>Please correct the following:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(isset($draft) && $draft)
    <div class="draft-notice">
        <strong>Resuming Draft Sale #{{ $draft->id }}</strong>
        <p style="margin: 6px 0;">
            Your saved products have been restored. Check quantities and
            current stock availability before completing checkout.
        </p>
        <a href="{{ route('sales.drafts') }}">Back to Draft Sales</a>
    </div>
@endif

<div class="shopping-cart-topbar">
    <a href="#shopping-cart"
       class="shopping-cart-top-link"
       aria-label="Go to shopping cart">
        <svg class="shopping-cart-icon"
             xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2"
             stroke-linecap="round"
             stroke-linejoin="round"
             aria-hidden="true">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <span>Shopping Cart</span>
        <span class="shopping-cart-badge" id="shopping-cart-badge">0</span>
    </a>
</div>

{{-- Available products --}}
<div class="card checkout-products">
    <div>
        <h2>Available Products</h2>
        <p class="text-muted">
            Search, filter, and select products to add to the shopping cart.
        </p>
    </div>

    @if($products->isEmpty())
        <div class="products-empty">
            <h3>No Products Available</h3>
            <p>No active products are currently in stock.</p>
        </div>
    @else
        @php
            $productCategories = $products
                ->pluck('category.name')
                ->filter()
                ->unique()
                ->sort()
                ->values();
        @endphp

        <div class="products-toolbar">
            <div class="toolbar-field">
                <label for="product-search">Search Products</label>
                <input type="search" id="product-search"
                       placeholder="Search name, SKU, brand or size..."
                       autocomplete="off">
            </div>

            <div class="toolbar-field">
                <label for="product-category">Category</label>
                <select id="product-category">
                    <option value="">All Categories</option>
                    @foreach($productCategories as $categoryName)
                        <option value="{{ $categoryName }}">{{ $categoryName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="toolbar-field">
                <label for="product-sort">Sort By</label>
                <select id="product-sort">
                    <option value="name-asc">Name: A to Z</option>
                    <option value="name-desc">Name: Z to A</option>
                    <option value="price-asc">Price: Low to High</option>
                    <option value="price-desc">Price: High to Low</option>
                    <option value="stock-desc">Stock: Highest First</option>
                    <option value="stock-asc">Stock: Lowest First</option>
                </select>
            </div>

            <button type="button" class="btn-secondary"
                    id="clear-product-filters">Clear</button>
        </div>

        <div class="products-summary">
            <span id="products-result-count">
                {{ $products->count() }} products available
            </span>
            <span>Prices shown in KSh</span>
        </div>

        <div class="products-grid" id="products-grid">
            @foreach($products as $product)
                @php
                    $stock = (float) $product->quantity_on_hand;
                    $price = (float) $product->selling_price;
                    $categoryName = $product->category?->name ?? '';
                    $brand = $product->brand ?? '';
                    $size = $product->size ?? '';
                    $unit = $product->unit ?? 'piece';
                @endphp

                <div class="checkout-product-card"
                     data-name="{{ strtolower($product->name) }}"
                     data-sku="{{ strtolower($product->sku) }}"
                     data-brand="{{ strtolower($brand) }}"
                     data-size="{{ strtolower($size) }}"
                     data-category="{{ $categoryName }}"
                     data-price="{{ $price }}"
                     data-stock="{{ $stock }}">

                    <div class="product-card-top">
                        <div>
                            <div class="product-card-name">{{ $product->name }}</div>
                            <div class="product-card-sku">SKU: {{ $product->sku }}</div>
                        </div>
                        <span class="product-stock-badge {{ $stock <= (float) $product->reorder_level ? 'low' : '' }}">
                            {{ $stock <= (float) $product->reorder_level ? 'Low Stock' : 'In Stock' }}
                        </span>
                    </div>

                    <div class="product-card-details">
                        <div class="product-card-detail">
                            <span class="product-card-label">Category</span>
                            <span class="product-card-value">{{ $categoryName ?: 'Uncategorized' }}</span>
                        </div>

                        @if($brand)
                            <div class="product-card-detail">
                                <span class="product-card-label">Brand</span>
                                <span class="product-card-value">{{ $brand }}</span>
                            </div>
                        @endif

                        @if($size)
                            <div class="product-card-detail">
                                <span class="product-card-label">Size</span>
                                <span class="product-card-value">{{ $size }}</span>
                            </div>
                        @endif

                        <div class="product-card-detail">
                            <span class="product-card-label">Available Stock</span>
                            <span class="product-card-value">
                                {{ number_format($stock, 3) }} {{ $unit }}
                            </span>
                        </div>
                    </div>

                    <div class="product-card-price">
                        KSh {{ number_format($price, 2) }}
                        <span style="font-size:12px;font-weight:400;color:#64748b;">
                            / {{ $unit }}
                        </span>
                    </div>

                    <button type="button"
                            class="product-add-button"
                            onclick="addProduct(
                                {{ $product->id }},
                                @js($product->name),
                                {{ $price }},
                                {{ $stock }}
                            )">
                        + Add to Cart
                    </button>
                </div>
            @endforeach

            <div class="products-empty" id="no-matching-products" hidden>
                <h3>No Matching Products</h3>
                <p>Try another search term or change your filters.</p>
            </div>
        </div>
    @endif
</div>

{{-- Shopping cart and checkout --}}
<div class="card" id="shopping-cart" style="margin-top:24px;">
    <div class="cart-heading">
        <h2>Shopping Cart</h2>
        <span class="text-muted" id="cart-item-count">0 items</span>
    </div>

    <div id="cart-message" class="cart-message" role="alert" hidden></div>
    <div id="cart-items"></div>

    <div class="cart-total">
        Total: KSh <span id="cart-total">0.00</span>
    </div>

    <form id="checkout-form"
          action="{{ route('sales.store') }}"
          method="POST">
        @csrf

        <input type="hidden" name="cart_data" id="cart-data">
        <input type="hidden" name="draft_id" id="draft-id"
               value="{{ isset($draft) && $draft ? $draft->id : '' }}">

        <label for="payment-method" class="checkout-payment-field">
            Payment Method
        </label>

        <select id="payment-method" name="payment_method"
                class="checkout-payment-select" required>
            <option value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="mpesa" {{ old('payment_method') === 'mpesa' ? 'selected' : '' }}>M-Pesa (STK Push)</option>
            <option value="split" {{ old('payment_method') === 'split' ? 'selected' : '' }}>Cash + M-Pesa (Split Payment)</option>
        </select>

        <div style="margin-top:16px;">
            <label for="cash-amount" class="checkout-payment-field">
                Cash Amount (KSh)
            </label>
            <input type="number"
                   name="cash_amount"
                   id="cash-amount"
                   class="checkout-phone-input"
                   value="{{ old('cash_amount', '0.00') }}"
                   min="0" step="0.01" required>
        </div>

        <div style="margin-top:16px;">
            <label for="mpesa-amount" class="checkout-payment-field">
                M-Pesa Amount (KSh)
            </label>
            <input type="number"
                   name="mpesa_amount"
                   id="mpesa-amount"
                   class="checkout-phone-input"
                   value="{{ old('mpesa_amount', '0.00') }}"
                   min="0" step="0.01" required>
        </div>

        <p class="text-muted" id="payment-help" style="margin-top:10px;">
            Cash and M-Pesa amounts must add up to the sale total.
        </p>

        {{-- Phone field: visible for M-Pesa, or split when M-Pesa > 0 --}}
        <div id="mpesa-phone-container" style="display:none; margin-top:16px;">
            <label for="mpesa-phone" class="checkout-payment-field">
                Customer M-Pesa Phone Number
            </label>

            <input type="tel"
                   name="mpesa_phone"
                   id="mpesa-phone"
                   class="checkout-phone-input"
                   value="{{ old('mpesa_phone') }}"
                   placeholder="e.g. 0712345678"
                   autocomplete="tel"
                   inputmode="tel"
                   pattern="(?:\+?254|0)[17][0-9]{8}"
                   maxlength="13">

            <p class="text-muted">
                An STK Push prompt will be sent to the customer's phone
                for the M-Pesa amount. The customer enters their PIN
                on their own phone.
            </p>
        </div>

        <div id="payment-error" class="cart-message" role="alert" hidden></div>

        <div class="draft-actions">
            <button type="submit"
                    id="save-draft"
                    class="btn-secondary checkout-submit"
                    value="save_draft"
                    formaction="{{ route('sales.drafts.store') }}"
                    formnovalidate>
                {{ isset($draft) && $draft ? 'Update Draft' : 'Save as Draft' }}
            </button>

            <button type="submit"
                    id="complete-sale"
                    class="checkout-submit"
                    value="complete_sale">
                Complete Sale
            </button>

            <a href="{{ route('sales.drafts') }}"
               class="btn-secondary" style="margin-top:18px;">
                View Draft Sales
            </a>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
'use strict';

const cart = new Map();
const savedDraftItems = @json($draftItems ?? []);

savedDraftItems.forEach(item => {
    const id = Number(item.id);
    const quantity = Number(item.quantity);
    const price = Number(item.price);
    const available = Number(item.available);

    if (
        !Number.isInteger(id) || id <= 0 ||
        !Number.isFinite(quantity) || quantity <= 0 ||
        !Number.isFinite(price) || price < 0
    ) {
        return;
    }

    cart.set(id, {
        id,
        name: String(item.name ?? 'Product'),
        price,
        available: Number.isFinite(available) ? available : 0,
        quantity
    });
});

function money(value) {
    return Math.round((value + Number.EPSILON) * 100) / 100;
}

function getSaleTotal() {
    let total = 0;

    for (const item of cart.values()) {
        total += item.price * item.quantity;
    }

    return money(total);
}

function showMessage(message) {
    const element = document.getElementById('cart-message');
    if (!element) return;

    element.textContent = message;
    element.hidden = !message;
}

function addProduct(id, name, price, available) {
    id = Number(id);
    price = Number(price);
    available = Number(available);

    const item = cart.get(id);
    const newQuantity = item ? item.quantity + 1 : 1;

    if (newQuantity > available) {
        showMessage(`Only ${available} units are available for ${name}.`);
        return;
    }

    if (item) {
        item.quantity = newQuantity;
    } else {
        cart.set(id, {
            id,
            name: String(name),
            price,
            available,
            quantity: 1
        });
    }

    showMessage('');
    renderCart();
}

function changeQuantity(id, value) {
    const item = cart.get(Number(id));
    if (!item) return;

    if (String(value).trim() === '') {
        showMessage('Enter a valid quantity.');
        renderCart();
        return;
    }

    const quantity = Number(value);

    if (!Number.isFinite(quantity) || quantity <= 0) {
        showMessage('Quantity must be greater than zero.');
        renderCart();
        return;
    }

    if (quantity > item.available) {
        showMessage(`Only ${item.available} units are available for ${item.name}.`);
        renderCart();
        return;
    }

    item.quantity = quantity;
    showMessage('');
    renderCart();
}

function removeProduct(id) {
    cart.delete(Number(id));
    showMessage('');
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-items');
    if (!container) return;

    container.replaceChildren();

    let total = 0;
    let itemCount = 0;

    if (cart.size === 0) {
        const empty = document.createElement('p');
        empty.className = 'text-muted';
        empty.textContent = 'Your cart is empty.';
        container.appendChild(empty);
    }

    for (const item of cart.values()) {
        const lineTotal = money(item.price * item.quantity);

        total += lineTotal;
        itemCount += item.quantity;

        const row = document.createElement('div');
        row.className = 'cart-line';

        const description = document.createElement('div');
        description.className = 'cart-line-description';

        const name = document.createElement('div');
        name.className = 'cart-line-name';
        name.textContent = item.name;

        const subtotal = document.createElement('div');
        subtotal.className = 'cart-line-subtotal';
        subtotal.textContent =
            `${item.quantity} × KSh ${item.price.toFixed(2)} = KSh ${lineTotal.toFixed(2)}`;

        description.append(name, subtotal);

        const quantity = document.createElement('input');
        quantity.type = 'number';
        quantity.min = '0.001';
        quantity.max = String(item.available);
        quantity.step = '0.001';
        quantity.value = String(item.quantity);
        quantity.setAttribute('aria-label', `Quantity for ${item.name}`);

        quantity.addEventListener('change', () => {
            changeQuantity(item.id, quantity.value);
        });

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'cart-remove-button';
        remove.textContent = 'Remove';
        remove.setAttribute('aria-label', `Remove ${item.name} from cart`);
        remove.addEventListener('click', () => removeProduct(item.id));

        row.append(description, quantity, remove);
        container.appendChild(row);
    }

    document.getElementById('cart-total').textContent =
        money(total).toFixed(2);

    document.getElementById('cart-item-count').textContent =
        `${Number(itemCount.toFixed(3))} ${itemCount === 1 ? 'item' : 'items'}`;

    const badge = document.getElementById('shopping-cart-badge');

    if (badge) {
        badge.textContent = cart.size > 99 ? '99+' : cart.size;
        badge.setAttribute('aria-label', `${cart.size} different products in cart`);
    }

    updatePaymentFields('cart');
}

/*
 * Payment behavior:
 * - Cash: full total in cash.
 * - M-Pesa: full total through STK Push and show phone field.
 * - Split: cash + M-Pesa must equal the total.
 * - Split with M-Pesa > 0: show phone field.
 */
function updatePaymentFields(source = 'initial') {
    const method = document.getElementById('payment-method');
    const cash = document.getElementById('cash-amount');
    const mpesa = document.getElementById('mpesa-amount');
    const container = document.getElementById('mpesa-phone-container');
    const phone = document.getElementById('mpesa-phone');
    const help = document.getElementById('payment-help');

    if (!method || !cash || !mpesa || !container || !phone) return;

    const total = getSaleTotal();

    if (method.value === 'cash') {
        cash.value = total.toFixed(2);
        mpesa.value = '0.00';
    } else if (method.value === 'mpesa') {
        cash.value = '0.00';
        mpesa.value = total.toFixed(2);
    } else if (method.value === 'split') {
        const currentCash = Number(cash.value);
        const currentMpesa = Number(mpesa.value);

        if (source === 'cart') {
            const validMpesa =
                mpesa.value.trim() !== '' &&
                Number.isFinite(currentMpesa) &&
                currentMpesa >= 0;

            const nextMpesa = validMpesa
                ? Math.min(currentMpesa, total)
                : 0;

            mpesa.value = nextMpesa.toFixed(2);
            cash.value = money(total - nextMpesa).toFixed(2);
        } else if (
            source === 'method' ||
            cash.value.trim() === '' ||
            mpesa.value.trim() === '' ||
            !Number.isFinite(currentCash) ||
            !Number.isFinite(currentMpesa) ||
            currentCash < 0 ||
            currentMpesa < 0 ||
            Math.abs(currentCash + currentMpesa - total) > 0.009
        ) {
            cash.value = total.toFixed(2);
            mpesa.value = '0.00';
        }
    }

    const isSplit = method.value === 'split';
    cash.readOnly = !isSplit;
    mpesa.readOnly = !isSplit;

    const mpesaAmount = Number(mpesa.value);

    /*
     * FIX:
     * Show the phone field whenever M-Pesa is selected, even if the
     * amount is temporarily zero. For split payments, show it only
     * when the M-Pesa portion is greater than zero.
     *
     * Use both hidden and display so the visibility is explicitly
     * controlled and does not depend on stale CSS or HTML state.
     */
    const needsPhone =
        method.value === 'mpesa' ||
        (method.value === 'split' &&
            Number.isFinite(mpesaAmount) &&
            mpesaAmount > 0);

    container.hidden = !needsPhone;
    container.style.display = needsPhone ? 'block' : 'none';
    phone.required = needsPhone;

    if (!needsPhone) {
        phone.value = '';
    }

    if (help) {
        if (method.value === 'split') {
            help.textContent =
                'Enter the cash and M-Pesa portions. Their sum must equal the sale total.';
        } else if (method.value === 'mpesa') {
            help.textContent =
                'The full sale total will be requested through M-Pesa STK Push.';
        } else {
            help.textContent = 'The full sale total will be paid in cash.';
        }
    }

    validatePaymentAmounts();
}

function validatePaymentAmounts() {
    const cash = document.getElementById('cash-amount');
    const mpesa = document.getElementById('mpesa-amount');
    const phone = document.getElementById('mpesa-phone');
    const error = document.getElementById('payment-error');
    const completeButton = document.getElementById('complete-sale');
    const method = document.getElementById('payment-method');

    if (!cash || !mpesa || !error || !phone || !method) {
        return false;
    }

    const total = getSaleTotal();
    const cashAmount = Number(cash.value);
    const mpesaAmount = Number(mpesa.value);
    const phoneValue = phone.value.trim();

    let message = '';

    if (cart.size === 0) {
        message = 'Add at least one product before completing the sale.';
    } else if (
        cash.value.trim() === '' ||
        mpesa.value.trim() === '' ||
        !Number.isFinite(cashAmount) ||
        !Number.isFinite(mpesaAmount) ||
        cashAmount < 0 ||
        mpesaAmount < 0
    ) {
        message = 'Enter valid, non-negative cash and M-Pesa amounts.';
    } else if (
        method.value === 'cash' &&
        (Math.abs(cashAmount - total) > 0.009 || mpesaAmount !== 0)
    ) {
        message = 'Cash payment must cover the full sale total.';
    } else if (
        method.value === 'mpesa' &&
        (cashAmount !== 0 || Math.abs(mpesaAmount - total) > 0.009)
    ) {
        message = 'M-Pesa payment must cover the full sale total.';
    } else if (
        Math.abs(cashAmount + mpesaAmount - total) > 0.009
    ) {
        message = `Cash and M-Pesa must add up to KSh ${total.toFixed(2)}.`;
    } else if (
        mpesaAmount > 0 &&
        Math.abs(mpesaAmount - Math.round(mpesaAmount)) > 0.001
    ) {
        message = 'The M-Pesa amount must be a whole number of shillings.';
    } else if (
        mpesaAmount > 0 &&
        !/^(?:\+?254|0)[17]\d{8}$/.test(phoneValue)
    ) {
        message = 'Enter a valid Kenyan M-Pesa phone number, e.g. 0712345678.';
    }

    error.textContent = message;
    error.hidden = !message;

    if (completeButton) {
        completeButton.disabled = Boolean(message);
    }

    return message === '';
}

function filterAndSortProducts() {
    const searchInput = document.getElementById('product-search');
    const categoryInput = document.getElementById('product-category');
    const sortInput = document.getElementById('product-sort');
    const grid = document.getElementById('products-grid');

    if (!searchInput || !categoryInput || !sortInput || !grid) return;

    const search = searchInput.value.trim().toLowerCase();
    const category = categoryInput.value;
    const sort = sortInput.value;

    const cards = Array.from(grid.querySelectorAll('.checkout-product-card'));

    cards.sort((a, b) => {
        const nameA = a.dataset.name || '';
        const nameB = b.dataset.name || '';
        const priceA = Number(a.dataset.price);
        const priceB = Number(b.dataset.price);
        const stockA = Number(a.dataset.stock);
        const stockB = Number(b.dataset.stock);

        switch (sort) {
            case 'name-desc': return nameB.localeCompare(nameA);
            case 'price-asc': return priceA - priceB;
            case 'price-desc': return priceB - priceA;
            case 'stock-asc': return stockA - stockB;
            case 'stock-desc': return stockB - stockA;
            default: return nameA.localeCompare(nameB);
        }
    });

    let visibleCount = 0;

    cards.forEach(card => {
        const searchableText = [
            card.dataset.name,
            card.dataset.sku,
            card.dataset.brand,
            card.dataset.size
        ].join(' ');

        const visible =
            searchableText.includes(search) &&
            (!category || card.dataset.category === category);

        card.hidden = !visible;

        if (visible) visibleCount++;

        grid.appendChild(card);
    });

    const empty = document.getElementById('no-matching-products');

    if (empty) {
        empty.hidden = visibleCount !== 0;
        grid.appendChild(empty);
    }

    const resultCount = document.getElementById('products-result-count');

    if (resultCount) {
        resultCount.textContent =
            `${visibleCount} ${visibleCount === 1 ? 'product' : 'products'} found`;
    }
}

function clearProductFilters() {
    const search = document.getElementById('product-search');
    const category = document.getElementById('product-category');
    const sort = document.getElementById('product-sort');

    if (search) search.value = '';
    if (category) category.value = '';
    if (sort) sort.value = 'name-asc';

    filterAndSortProducts();
}

document.getElementById('product-search')
    ?.addEventListener('input', filterAndSortProducts);

document.getElementById('product-category')
    ?.addEventListener('change', filterAndSortProducts);

document.getElementById('product-sort')
    ?.addEventListener('change', filterAndSortProducts);

document.getElementById('clear-product-filters')
    ?.addEventListener('click', clearProductFilters);

document.getElementById('payment-method')
    ?.addEventListener('change', () => updatePaymentFields('method'));

document.getElementById('cash-amount')
    ?.addEventListener('input', validatePaymentAmounts);

document.getElementById('mpesa-amount')
    ?.addEventListener('input', () => {
        const method = document.getElementById('payment-method');
        const cash = document.getElementById('cash-amount');
        const mpesa = document.getElementById('mpesa-amount');
        const container = document.getElementById('mpesa-phone-container');
        const phone = document.getElementById('mpesa-phone');

        const amount = Number(mpesa.value);
        const needsPhone =
            method?.value === 'mpesa' ||
            (method?.value === 'split' &&
                mpesa.value.trim() !== '' &&
                Number.isFinite(amount) &&
                amount > 0);

        if (container && phone) {
            container.hidden = !needsPhone;
            container.style.display = needsPhone ? 'block' : 'none';
            phone.required = needsPhone;

            if (!needsPhone) {
                phone.value = '';
            }
        }

        // In split mode, calculate the cash balance without overwriting
        // the cashier's M-Pesa input.
        if (
            method?.value === 'split' &&
            cash &&
            mpesa.value.trim() !== '' &&
            Number.isFinite(amount) &&
            amount >= 0 &&
            amount <= getSaleTotal()
        ) {
            cash.value = money(getSaleTotal() - amount).toFixed(2);
        }

        validatePaymentAmounts();
    });

document.getElementById('mpesa-phone')
    ?.addEventListener('input', validatePaymentAmounts);

const checkoutForm = document.getElementById('checkout-form');

if (checkoutForm) {
    checkoutForm.addEventListener('submit', function(event) {
        const submitter = event.submitter;
        const isSavingDraft = submitter?.id === 'save-draft';
        const isCompletingSale = submitter?.id === 'complete-sale';

        if (!isSavingDraft && !isCompletingSale) {
            event.preventDefault();
            return;
        }

        if (cart.size === 0) {
            event.preventDefault();
            showMessage('Add at least one product before continuing.');
            return;
        }

        if (isCompletingSale && !validatePaymentAmounts()) {
            event.preventDefault();
            return;
        }

        const items = Array.from(cart.values()).map(item => ({
            id: item.id,
            quantity: item.quantity
        }));

        document.getElementById('cart-data').value = JSON.stringify(items);

        if (isSavingDraft) {
            // Do not run payment or phone validation when saving a draft.
            submitter.disabled = true;
            submitter.textContent = 'Saving Draft...';
            return;
        }

        // Prevent duplicate checkout submissions.
        submitter.disabled = true;
        submitter.textContent = 'Processing...';
    });
}

// Initialize without changing the original page design.
filterAndSortProducts();
renderCart();
updatePaymentFields('initial');
</script>
@endpush
