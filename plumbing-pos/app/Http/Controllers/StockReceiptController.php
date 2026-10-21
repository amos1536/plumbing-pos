<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StockReceiptController extends Controller
{
    public function create()
    {
        $suppliers = Supplier::where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('stock.receive', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'integer',
                Rule::exists('suppliers', 'id')
                    ->where('is_active', true),
            ],
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')
                    ->where('is_active', true),
            ],
            'purchase_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit_cost' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $product = Product::whereKey($validated['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $quantity = (float) $validated['quantity'];
            $unitCost = (float) $validated['unit_cost'];
            $lineTotal = round($quantity * $unitCost, 2);

            $reference = 'PUR-' . now()->format('YmdHis')
                . '-' . strtoupper(bin2hex(random_bytes(3)));

            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'reference' => $reference,
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $lineTotal,
                'status' => 'received',
                'notes' => $validated['notes'] ?? null,
            ]);

            $purchase->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'line_total' => $lineTotal,
            ]);

            // Increase the product's stock.
            $product->quantity_on_hand =
                (float) $product->quantity_on_hand + $quantity;

            $product->save();

            // Record the stock movement and the user responsible.
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'purchase',
                'quantity' => $quantity,
                'reference' => $reference,
                'notes' => 'Stock received from supplier',
                'user_id' => Auth::id(),
            ]);

            // Record the stock receipt in the audit log.
            AuditLogger::log(
                'stock_received',
                'purchase',
                (int) $purchase->id,
                "Received {$quantity} units of product '{$product->name}' "
                    . "(SKU: {$product->sku}) from supplier. "
                    . "Purchase reference: {$reference}.",
                $request
            );
        });

        return redirect()
            ->route('stock.index')
            ->with(
                'success',
                'Stock received and inventory updated successfully.'
            );
    }
}
