<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleReturnController extends Controller
{
    /**
     * Display return history.
     */
    public function index()
    {
        $returns = DB::table('sale_returns')
            ->join('sales', 'sale_returns.sale_id', '=', 'sales.id')
            ->leftJoin('users', 'sale_returns.user_id', '=', 'users.id')
            ->select(
                'sale_returns.*',
                'sales.receipt_number',
                'users.name as cashier_name'
            )
            ->orderByDesc('sale_returns.created_at')
            ->paginate(15);

        return view('sales-returns.index', compact('returns'));
    }

    /**
     * Display the return form for a completed sale.
     */
    public function create(Sale $sale)
    {
        if ($sale->status !== 'completed') {
            return redirect()
                ->route('sales.index')
                ->with('error', 'Only completed sales can be returned.');
        }

        $sale->load('items.product');

        $returnedQuantities = DB::table('sale_returns')
            ->where('sale_id', $sale->id)
            ->where('status', 'completed')
            ->select(
                'sale_item_id',
                DB::raw('SUM(quantity) as total_returned')
            )
            ->groupBy('sale_item_id')
            ->pluck('total_returned', 'sale_item_id');

        return view(
            'sales-returns.create',
            compact('sale', 'returnedQuantities')
        );
    }

    /**
     * Record a completed return, restore inventory,
     * and create an audit log entry.
     */
    public function store(Request $request, Sale $sale)
    {
        if ($sale->status !== 'completed') {
            return redirect()
                ->route('sales.index')
                ->with('error', 'Only completed sales can be returned.');
        }

        $validated = $request->validate([
            'sale_item_id' => ['required', 'integer', 'min:1'],
            'quantity' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,3',
            ],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $sale, $request) {
            $saleItem = SaleItem::where('sale_id', $sale->id)
                ->where('id', $validated['sale_item_id'])
                ->lockForUpdate()
                ->first();

            if (!$saleItem) {
                throw ValidationException::withMessages([
                    'sale_item_id' =>
                        'The selected item does not belong to this sale.',
                ]);
            }

            $quantity = (float) $validated['quantity'];
            $originalQuantity = (float) $saleItem->quantity;

            $alreadyReturned = (float) DB::table('sale_returns')
                ->where('sale_item_id', $saleItem->id)
                ->where('status', 'completed')
                ->sum('quantity');

            $remainingQuantity = $originalQuantity - $alreadyReturned;

            if ($quantity > $remainingQuantity + 0.000001) {
                throw ValidationException::withMessages([
                    'quantity' => 'Only '
                        . number_format(max(0, $remainingQuantity), 3)
                        . ' units remain eligible for return.',
                ]);
            }

            $product = Product::where('id', $saleItem->product_id)
                ->lockForUpdate()
                ->first();

            if (!$product) {
                throw ValidationException::withMessages([
                    'sale_item_id' =>
                        'The product for this sale item no longer exists.',
                ]);
            }

            // Calculate the refund using the original sale line.
            $lineAmount = max(
                0,
                (float) $saleItem->line_total
                    - (float) $saleItem->discount
            );

            $unitRefund = $originalQuantity > 0
                ? $lineAmount / $originalQuantity
                : 0;

            $refundAmount = round($unitRefund * $quantity, 2);

            // Record the return.
            $returnId = DB::table('sale_returns')->insertGetId([
                'sale_id' => $sale->id,
                'sale_item_id' => $saleItem->id,
                'user_id' => Auth::id(),
                'quantity' => $quantity,
                'refund_amount' => $refundAmount,
                'reason' => $validated['reason'],
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Restore stock exactly once.
            $product->quantity_on_hand =
                (float) $product->quantity_on_hand + $quantity;

            $product->save();

            // Record the stock movement.
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'return',
                'quantity' => $quantity,
                'reference' => 'RETURN-' . $returnId,
                'notes' => 'Return for receipt '
                    . $sale->receipt_number
                    . '. Reason: ' . $validated['reason'],
                'user_id' => Auth::id(),
            ]);

            // Record the return in the audit log.
            AuditLogger::log(
                'sale_returned',
                'sale_return',
                (int) $returnId,
                "Return RETURN-{$returnId} recorded for receipt "
                    . "{$sale->receipt_number}. "
                    . "Product: {$product->name} (SKU: {$product->sku}). "
                    . "Quantity returned: {$quantity}. "
                    . "Refund amount: " . number_format($refundAmount, 2)
                    . ". Reason: {$validated['reason']}.",
                $request
            );
        });

        return redirect()
            ->route('sales-returns.index')
            ->with(
                'success',
                'Return completed. Inventory has been updated.'
            );
    }
}
