<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search by product name or SKU.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by category.
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Retrieve 12 products per page.
        $products = $query->latest()
            ->paginate(12)
            ->withQueryString();

        // Retrieve categories for the dropdown and category buttons.
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact(
            'products',
            'categories'
        ));
    }

    /**
     * Show the create product form.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    /**
     * Store a new product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:255', 'unique:products,barcode'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'quantity_on_hand' => ['required', 'numeric', 'min:0'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['is_active'] = true;

        $product = Product::create($validated);

        // Record opening stock in the stock movement history.
        if ((float) $product->quantity_on_hand > 0) {
            $product->stockMovements()->create([
                'type' => 'opening',
                'quantity' => $product->quantity_on_hand,
                'notes' => 'Opening stock when product was registered',
            ]);
        }

        AuditLogger::log(
            'created',
            'product',
            $product->id,
            "Created product '{$product->name}' (SKU: {$product->sku})."
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Product added successfully.');
    }

    /**
     * Display a product.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the edit product form.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update product details.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => [
                'required', 'string', 'max:255',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'barcode' => [
                'nullable', 'string', 'max:255',
                Rule::unique('products', 'barcode')->ignore($product->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
        ]);

        // Do not change stock quantity through product editing.
        $oldName = $product->name;
        $oldSku = $product->sku;
        $oldSellingPrice = $product->selling_price;
        $oldCostPrice = $product->cost_price;

        $product->update($validated);

        $changes = [];

        if ($oldName !== $product->name) {
            $changes[] = "name changed from '{$oldName}' to '{$product->name}'";
        }

        if ($oldSku !== $product->sku) {
            $changes[] = "SKU changed from '{$oldSku}' to '{$product->sku}'";
        }

        if ((float) $oldSellingPrice !== (float) $product->selling_price) {
            $changes[] = "selling price changed from {$oldSellingPrice} to {$product->selling_price}";
        }

        if ((float) $oldCostPrice !== (float) $product->cost_price) {
            $changes[] = "cost price changed from {$oldCostPrice} to {$product->cost_price}";
        }

        AuditLogger::log(
            'updated',
            'product',
            $product->id,
            $changes
                ? "Updated product '{$product->name}': " . implode('; ', $changes) . '.'
                : "Updated details for product '{$product->name}' (SKU: {$product->sku})."
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Deactivate a product instead of deleting its history.
     */
    public function destroy(Product $product)
    {
        if (!$product->is_active) {
            return redirect()
                ->route('products.index')
                ->with('success', 'Product is already deactivated.');
        }

        $productName = $product->name;
        $productSku = $product->sku;

        $product->update(['is_active' => false]);

        AuditLogger::log(
            'deactivated',
            'product',
            $product->id,
            "Deactivated product '{$productName}' (SKU: {$productSku})."
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deactivated successfully.');
    }
}
