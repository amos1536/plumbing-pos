<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Services\MpesaService;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class SaleController extends Controller
{
    /**
     * Display sales history.
     */
    public function index()
    {
        $sales = Sale::with('user')
            ->latest()
            ->paginate(15);

        return view('sales.index', compact('sales'));
    }

    /**
     * Display the checkout page.
     */
    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('quantity_on_hand', '>', 0)
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('sales.create', [
            'products' => $products,
            'draft' => null,
            'draftItems' => collect(),
        ]);
    }

    /**
     * Display saved drafts.
     */
    public function drafts()
    {
        $drafts = Sale::with(['user', 'items.product'])
            ->where('status', 'draft')
            ->latest()
            ->paginate(15);

        return view('sales.drafts', compact('drafts'));
    }

    /**
     * Save a new draft or update an existing draft.
     * Drafts do not deduct stock or record payments.
     */
    public function saveDraft(Request $request)
    {
        $cart = $this->validatedCart($request);

        $request->validate([
            'draft_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $quantities = $this->cartQuantities($cart);
        $draftId = $request->input('draft_id');

        $draft = DB::transaction(function () use (
            $request,
            $quantities,
            $draftId
        ) {
            $products = Product::whereIn('id', array_keys($quantities))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($quantities)) {
                throw ValidationException::withMessages([
                    'cart_data' => 'One or more products no longer exist.',
                ]);
            }

            $subtotal = 0;

            foreach ($quantities as $id => $quantity) {
                $product = $products->get($id);

                if (!$product->is_active) {
                    throw ValidationException::withMessages([
                        'cart_data' => "{$product->name} is inactive.",
                    ]);
                }

                if ($quantity > (float) $product->quantity_on_hand) {
                    throw ValidationException::withMessages([
                        'cart_data' =>
                            "Insufficient stock for {$product->name}. "
                            . "Available: {$product->quantity_on_hand}",
                    ]);
                }

                $subtotal += $quantity * (float) $product->selling_price;
            }

            $subtotal = round($subtotal, 2);

            if ($subtotal <= 0) {
                throw ValidationException::withMessages([
                    'cart_data' => 'The draft total must be greater than zero.',
                ]);
            }

            if ($draftId) {
                $draft = Sale::whereKey($draftId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $draft->status !== 'draft'
                    || (int) $draft->user_id !== (int) Auth::id()
                ) {
                    throw ValidationException::withMessages([
                        'draft_id' =>
                            'This draft is no longer available to update.',
                    ]);
                }

                $draft->subtotal = $subtotal;
                $draft->discount = 0;
                $draft->total_amount = $subtotal;
                $draft->save();

                $draft->items()->delete();
            } else {
                $draft = Sale::create([
                    'sale_uuid' => (string) Str::uuid(),
                    'receipt_number' => 'DRAFT-' . now()->format('YmdHis')
                        . '-' . Str::upper(Str::random(6)),
                    'user_id' => Auth::id(),
                    'subtotal' => $subtotal,
                    'discount' => 0,
                    'total_amount' => $subtotal,
                    'status' => 'draft',
                    'sold_at' => null,
                ]);
            }

            foreach ($quantities as $id => $quantity) {
                $product = $products->get($id);
                $price = (float) $product->selling_price;

                SaleItem::create([
                    'sale_id' => $draft->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'cost_price' => (float) $product->cost_price,
                    'discount' => 0,
                    'line_total' => round($quantity * $price, 2),
                ]);
            }

            return $draft;
        });

        AuditLogger::log(
            'sale_draft_saved',
            'sale',
            (int) $draft->id,
            "Draft {$draft->receipt_number} saved or updated.",
            $request
        );

        return redirect()
            ->route('sales.drafts')
            ->with(
                'success',
                'Draft saved successfully. No payment was recorded and stock was not deducted.'
            );
    }

    /**
     * Resume a saved draft.
     */
    public function resumeDraft(Sale $sale)
    {
        abort_unless(
            $sale->status === 'draft'
            && (int) $sale->user_id === (int) Auth::id(),
            404
        );

        $products = Product::where('is_active', true)
            ->where('quantity_on_hand', '>', 0)
            ->with('category')
            ->orderBy('name')
            ->get();

        $draftItems = $sale->items()
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'name' => $item->product->name,
                    'price' => (float) $item->unit_price,
                    'available' => (float) $item->product->quantity_on_hand,
                    'quantity' => (float) $item->quantity,
                ];
            });

        return view('sales.create', [
            'products' => $products,
            'draft' => $sale,
            'draftItems' => $draftItems,
        ]);
    }

    /**
     * Process cash, M-Pesa, or split-payment checkout.
     *
     * Cash is recorded as confirmed.
     * M-Pesa is recorded as pending until its callback confirms payment.
     *
     * IMPORTANT:
     * The M-Pesa request is made after the database transaction commits.
     */
    public function store(Request $request, MpesaService $mpesaService)
    {
        $cart = $this->validatedCart($request);

        // Validate the amounts as supplied by the checkout form.
        $request->validate([
            'payment_method' => [
                'required',
                'in:cash,mpesa,split',
            ],
            'cash_amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'mpesa_amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'mpesa_phone' => [
                'nullable',
                'string',
                'regex:/^(?:\+?254|0)[17][0-9]{8}$/',
            ],
            'draft_id' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        $method = $request->input('payment_method');

        $cashAmount = round((float) $request->input('cash_amount'), 2);
        $mpesaAmount = round((float) $request->input('mpesa_amount'), 2);

        /*
         * Enforce the selected payment method on the server.
         * Do not rely on JavaScript alone for payment validation.
         */
        if ($method === 'cash' && $mpesaAmount != 0.0) {
            throw ValidationException::withMessages([
                'mpesa_amount' =>
                    'For cash-only payments, the M-Pesa amount must be zero.',
            ]);
        }

        if ($method === 'mpesa' && $cashAmount != 0.0) {
            throw ValidationException::withMessages([
                'cash_amount' =>
                    'For M-Pesa-only payments, the cash amount must be zero.',
            ]);
        }

        if ($method === 'cash' && $cashAmount <= 0) {
            throw ValidationException::withMessages([
                'cash_amount' => 'Enter the cash payment amount.',
            ]);
        }

        if ($method === 'mpesa' && $mpesaAmount <= 0) {
            throw ValidationException::withMessages([
                'mpesa_amount' => 'Enter the M-Pesa payment amount.',
            ]);
        }

        if ($method === 'split' && ($cashAmount <= 0 || $mpesaAmount <= 0)) {
            throw ValidationException::withMessages([
                'cash_amount' =>
                    'For split payments, both cash and M-Pesa amounts must be greater than zero.',
            ]);
        }

        if ($cashAmount <= 0 && $mpesaAmount <= 0) {
            throw ValidationException::withMessages([
                'cash_amount' =>
                    'Enter a cash amount, an M-Pesa amount, or both.',
            ]);
        }

        // Safaricom Daraja STK Push accepts a whole-shilling amount.
        if (abs($mpesaAmount - round($mpesaAmount)) > 0.00001) {
            throw ValidationException::withMessages([
                'mpesa_amount' =>
                    'The M-Pesa amount must be a whole-number KSh amount.',
            ]);
        }

        $phone = null;

        if ($mpesaAmount > 0) {
            if (!$request->filled('mpesa_phone')) {
                throw ValidationException::withMessages([
                    'mpesa_phone' =>
                        'Enter the customer M-Pesa phone number.',
                ]);
            }

            $phone = $this->normalizeMpesaPhone(
                (string) $request->input('mpesa_phone')
            );
        }

        $quantities = $this->cartQuantities($cart);

        /*
         * Validate stock and calculate the authoritative sale total
         * using current database prices.
         */
        $result = DB::transaction(function () use (
            $request,
            $quantities,
            $cashAmount,
            $mpesaAmount
        ) {
            $products = Product::whereIn('id', array_keys($quantities))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($quantities)) {
                throw ValidationException::withMessages([
                    'cart_data' => 'One or more products no longer exist.',
                ]);
            }

            $subtotal = 0;

            foreach ($quantities as $id => $quantity) {
                $product = $products->get($id);

                if (!$product->is_active) {
                    throw ValidationException::withMessages([
                        'cart_data' => "{$product->name} is inactive.",
                    ]);
                }

                if ($quantity > (float) $product->quantity_on_hand) {
                    throw ValidationException::withMessages([
                        'cart_data' =>
                            "Insufficient stock for {$product->name}. "
                            . "Available: {$product->quantity_on_hand}",
                    ]);
                }

                $subtotal += $quantity * (float) $product->selling_price;
            }

            $subtotal = round($subtotal, 2);

            if ($subtotal <= 0) {
                throw ValidationException::withMessages([
                    'cart_data' => 'The sale total must be greater than zero.',
                ]);
            }

            if (abs(($cashAmount + $mpesaAmount) - $subtotal) > 0.009) {
                throw ValidationException::withMessages([
                    'cash_amount' =>
                        'Cash and M-Pesa amounts must add up to the full sale total of KSh '
                        . number_format($subtotal, 2) . '.',
                ]);
            }

            $draftId = $request->input('draft_id');

            if ($draftId) {
                $sale = Sale::whereKey($draftId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $sale->status !== 'draft'
                    || (int) $sale->user_id !== (int) Auth::id()
                ) {
                    throw ValidationException::withMessages([
                        'draft_id' =>
                            'This draft is no longer available to resume.',
                    ]);
                }

                $sale->receipt_number = 'POS-' . now()->format('YmdHis')
                    . '-' . Str::upper(Str::random(6));
                $sale->subtotal = $subtotal;
                $sale->discount = 0;
                $sale->total_amount = $subtotal;
                $sale->sold_at = now();
                $sale->status = $mpesaAmount > 0 ? 'pending' : 'completed';
                $sale->save();

                $sale->items()->delete();
            } else {
                $sale = Sale::create([
                    'sale_uuid' => (string) Str::uuid(),
                    'receipt_number' => 'POS-' . now()->format('YmdHis')
                        . '-' . Str::upper(Str::random(6)),
                    'user_id' => Auth::id(),
                    'subtotal' => $subtotal,
                    'discount' => 0,
                    'total_amount' => $subtotal,
                    'status' => $mpesaAmount > 0 ? 'pending' : 'completed',
                    'sold_at' => now(),
                ]);
            }

            foreach ($quantities as $id => $quantity) {
                $product = $products->get($id);
                $price = (float) $product->selling_price;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'cost_price' => (float) $product->cost_price,
                    'discount' => 0,
                    'line_total' => round($quantity * $price, 2),
                ]);

                // Deduct stock once for this checkout.
                $product->quantity_on_hand =
                    (float) $product->quantity_on_hand - $quantity;

                $product->save();

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity' => -$quantity,
                    'reference' => $sale->receipt_number,
                    'notes' => 'Stock issued for POS sale',
                    'user_id' => Auth::id(),
                ]);
            }

            if ($cashAmount > 0) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'method' => 'cash',
                    'amount' => $cashAmount,
                    'transaction_reference' => null,
                    'status' => 'confirmed',
                    'paid_at' => now(),
                    'mpesa_phone_number' => null,
                ]);
            }

            $mpesaPayment = null;

            if ($mpesaAmount > 0) {
                $mpesaPayment = Payment::create([
                    'sale_id' => $sale->id,
                    'method' => 'mpesa',
                    'amount' => $mpesaAmount,
                    'transaction_reference' => null,
                    'status' => 'pending',
                    'paid_at' => null,
                    'mpesa_phone_number' => null,
                ]);

                /*
                 * Do not mark M-Pesa as paid before Safaricom confirms it.
                 * The normalized phone number is attached after the
                 * transaction commits.
                 */
            }

            AuditLogger::log(
                'sale_recorded',
                'sale',
                (int) $sale->id,
                "Sale {$sale->receipt_number} recorded. Total: "
                    . number_format($subtotal, 2)
                    . ". Cash: " . number_format($cashAmount, 2)
                    . ". M-Pesa: " . number_format($mpesaAmount, 2) . ".",
                $request
            );

            return [
                'sale' => $sale,
                'mpesa_payment_id' => $mpesaPayment?->id,
            ];
        });

        $sale = $result['sale'];
        $mpesaPaymentId = $result['mpesa_payment_id'];

        // Cash-only sale: no Daraja request is necessary.
        if (!$mpesaPaymentId) {
            return redirect()
                ->route('sales.index')
                ->with(
                    'success',
                    'Sale completed successfully. Receipt: '
                    . $sale->receipt_number
                );
        }

        /*
         * Start STK Push after the database transaction has committed.
         * If Daraja fails, the pending payment remains visible for
         * reconciliation; do not automatically charge the customer again.
         */
        try {
            $payment = Payment::whereKey($mpesaPaymentId)->firstOrFail();

            $payment->mpesa_phone_number = $phone;
            $payment->save();

            $response = $mpesaService->initiateStkPush(
                $phone,
                (int) round((float) $payment->amount),
                $sale->receipt_number
            );

            if (
                empty($response['CheckoutRequestID'])
                || empty($response['MerchantRequestID'])
            ) {
                throw new \RuntimeException(
                    'Daraja did not return the expected STK Push request IDs.'
                );
            }

            DB::transaction(function () use ($payment, $response) {
                $lockedPayment = Payment::whereKey($payment->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedPayment->status !== 'pending') {
                    return;
                }

                $lockedPayment->mpesa_checkout_request_id =
                    $response['CheckoutRequestID'];

                $lockedPayment->mpesa_merchant_request_id =
                    $response['MerchantRequestID'];

                $lockedPayment->mpesa_result_description =
                    $response['ResponseDescription']
                    ?? 'STK Push request accepted by Daraja.';

                $lockedPayment->save();
            });

            return redirect()
                ->route('sales.index')
                ->with(
                    'success',
                    'Sale recorded. An M-Pesa payment prompt was requested '
                    . 'for KSh ' . number_format($mpesaAmount, 2)
                    . ' on ' . $phone
                    . '. The sale remains pending until payment is confirmed.'
                );
        } catch (Throwable $exception) {
            Log::error('Could not initiate M-Pesa STK Push.', [
                'sale_id' => $sale->id,
                'payment_id' => $mpesaPaymentId,
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('sales.index')
                ->with(
                    'warning',
                    'The sale has been recorded, but the M-Pesa request '
                    . 'could not be confirmed. The payment remains pending. '
                    . 'Check its status before attempting another charge.'
                );
        }
    }

    /**
     * Validate and decode the submitted cart JSON.
     */
    private function validatedCart(Request $request): array
    {
        $request->validate([
            'cart_data' => ['required', 'json'],
        ]);

        $cart = json_decode($request->input('cart_data'), true);

        if (!is_array($cart) || empty($cart) || !array_is_list($cart)) {
            throw ValidationException::withMessages([
                'cart_data' => 'Your cart is empty or invalid.',
            ]);
        }

        Validator::make(
            ['items' => $cart],
            [
                'items' => ['required', 'array', 'min:1'],
                'items.*.id' => [
                    'required',
                    'integer',
                    'distinct',
                    'min:1',
                ],
                'items.*.quantity' => [
                    'required',
                    'numeric',
                    'gt:0',
                ],
            ]
        )->validate();

        return $cart;
    }

    /**
     * Convert cart entries into product ID => quantity pairs.
     */
    private function cartQuantities(array $cart): array
    {
        $quantities = [];

        foreach ($cart as $item) {
            $id = (int) $item['id'];
            $quantity = (float) $item['quantity'];

            if (!is_finite($quantity) || $quantity <= 0) {
                throw ValidationException::withMessages([
                    'cart_data' => 'One or more quantities are invalid.',
                ]);
            }

            $quantities[$id] = $quantity;
        }

        return $quantities;
    }

    /**
     * Normalize Kenyan phone numbers to 254XXXXXXXXX.
     */
    private function normalizeMpesaPhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-]/', '', trim($phone));

        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        if (preg_match('/^0[17][0-9]{8}$/', $phone)) {
            $phone = '254' . substr($phone, 1);
        }

        if (!preg_match('/^254[17][0-9]{8}$/', $phone)) {
            throw ValidationException::withMessages([
                'mpesa_phone' =>
                    'Enter a valid Kenyan phone number, such as 0712345678.',
            ]);
        }

        return $phone;
    }

    /**
     * Display a sale receipt.
     */
    public function receipt(Sale $sale)
    {
        $sale->load([
            'user',
            'items.product',
            'payments',
        ]);

        return view('sales.receipt', compact('sale'));
    }
}
