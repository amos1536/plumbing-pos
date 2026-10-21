<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function callback(Request $request, string $token)
    {
        $expectedToken = (string) config('services.mpesa.callback_token');

        if (
            $expectedToken === '' ||
            !hash_equals($expectedToken, $token)
        ) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $callback = $request->input('Body.stkCallback');

        if (!is_array($callback)) {
            Log::warning('Invalid M-Pesa callback payload.');

            return response()->json(['Result' => 'Invalid payload'], 400);
        }

        $checkoutId = $callback['CheckoutRequestID'] ?? null;
        $merchantId = $callback['MerchantRequestID'] ?? null;
        $resultCode = $callback['ResultCode'] ?? null;
        $description = $callback['ResultDesc'] ?? 'No result description';

        if (!$checkoutId || !is_numeric($resultCode)) {
            return response()->json(['Result' => 'Invalid callback'], 400);
        }

        $items = collect(
            data_get($callback, 'CallbackMetadata.Item', [])
        )->keyBy('Name');

        try {
            DB::transaction(function () use (
                $checkoutId,
                $merchantId,
                $resultCode,
                $description,
                $items
            ) {
                $payment = Payment::where(
                    'mpesa_checkout_request_id',
                    $checkoutId
                )->lockForUpdate()->first();

                if (!$payment) {
                    Log::warning('M-Pesa callback has no matching payment.', [
                        'checkout_request_id' => $checkoutId,
                    ]);

                    return;
                }

                // Ignore duplicate callbacks for payments already processed.
                if ($payment->status !== 'pending') {
                    return;
                }

                $sale = Sale::whereKey($payment->sale_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $payment->mpesa_merchant_request_id = $merchantId;
                $payment->mpesa_result_code = (string) $resultCode;
                $payment->mpesa_result_description = $description;

                if ((int) $resultCode === 0) {
                    $receipt = data_get(
                        $items->get('MpesaReceiptNumber'),
                        'Value'
                    );

                    $amount = data_get($items->get('Amount'), 'Value');
                    $phone = data_get($items->get('PhoneNumber'), 'Value');

                    // Verify the transaction details before confirming payment.
                    if (
                        !$receipt ||
                        !is_numeric($amount) ||
                        (int) $amount !== (int) round((float) $payment->amount) ||
                        !$phone ||
                        (string) $phone !== (string) $payment->mpesa_phone_number
                    ) {
                        Log::warning('M-Pesa callback details did not match.', [
                            'checkout_request_id' => $checkoutId,
                            'payment_id' => $payment->id,
                        ]);

                        $payment->save();

                        return;
                    }

                    // Prevent duplicate M-Pesa receipts.
                    $receiptExists = Payment::where(
                        'transaction_reference',
                        $receipt
                    )->where('id', '!=', $payment->id)->exists();

                    if ($receiptExists) {
                        Log::warning('Duplicate M-Pesa receipt received.', [
                            'receipt' => $receipt,
                            'payment_id' => $payment->id,
                        ]);

                        $payment->save();

                        return;
                    }

                    $payment->transaction_reference = $receipt;
                    $payment->status = 'confirmed';
                    $payment->paid_at = now();
                    $payment->save();
                } else {
                    // Do not cancel the entire sale if this payment fails.
                    // A cash portion or another confirmed payment may exist.
                    $payment->status = 'failed';
                    $payment->save();

                    Log::warning('M-Pesa payment failed or was cancelled.', [
                        'sale_id' => $sale->id,
                        'payment_id' => $payment->id,
                        'result_code' => $resultCode,
                        'description' => $description,
                    ]);
                }

                // Recalculate the confirmed amount for the whole sale.
                $confirmedTotal = (float) Payment::where('sale_id', $sale->id)
                    ->where('status', 'confirmed')
                    ->sum('amount');

                $saleTotal = (float) $sale->total_amount;

                if ($confirmedTotal + 0.009 >= $saleTotal) {
                    $sale->status = 'completed';
                } elseif ($sale->status !== 'cancelled') {
                    $sale->status = 'pending';
                }

                $sale->save();
            });
        } catch (\Throwable $exception) {
            Log::error('M-Pesa callback processing failed.', [
                'checkout_request_id' => $checkoutId,
                'error' => $exception->getMessage(),
            ]);

            return response()->json(
                ['Result' => 'Processing error'],
                500
            );
        }

        return response()->json(['Result' => 'Accepted']);
    }
}
