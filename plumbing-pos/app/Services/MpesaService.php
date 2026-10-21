<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MpesaService
{
    private function baseUrl(): string
    {
        if (config('services.mpesa.environment') !== 'sandbox') {
            throw new RuntimeException(
                'Live M-Pesa integration has not been enabled.'
            );
        }

        return 'https://sandbox.safaricom.co.ke';
    }

    private function accessToken(): string
    {
        return Cache::remember(
            'mpesa_sandbox_access_token',
            now()->addMinutes(50),
            function () {
                $key = config('services.mpesa.consumer_key');
                $secret = config('services.mpesa.consumer_secret');

                if (!$key || !$secret) {
                    throw new RuntimeException(
                        'Daraja sandbox credentials are missing.'
                    );
                }

                $response = Http::withBasicAuth($key, $secret)
                    ->acceptJson()
                    ->timeout(20)
                    ->get(
                        $this->baseUrl()
                        . '/oauth/v1/generate',
                        ['grant_type' => 'client_credentials']
                    );

                $response->throw();

                $token = $response->json('access_token');

                if (!$token) {
                    throw new RuntimeException(
                        'Daraja did not return an access token.'
                    );
                }

                return $token;
            }
        );
    }

    public function initiateStkPush(
        string $phone,
        int $amount,
        string $accountReference
    ): array {
        if ($amount < 1) {
            throw new RuntimeException(
                'M-Pesa amount must be at least KSh 1.'
            );
        }

        $phone = $this->normalizePhone($phone);

        $shortcode = config('services.mpesa.shortcode');
        $passkey = config('services.mpesa.passkey');
        $callbackUrl = config('services.mpesa.callback_url');
        $tillNumber = config('services.mpesa.till_number');

        if (!$shortcode || !$passkey || !$callbackUrl || !$tillNumber) {
            throw new RuntimeException(
                'M-Pesa sandbox configuration is incomplete.'
            );
        }

        if (!str_starts_with($callbackUrl, 'https://')) {
            throw new RuntimeException(
                'The M-Pesa callback URL must use HTTPS.'
            );
        }

        $timestamp = now()->format('YmdHis');

        $password = base64_encode(
            $shortcode . $passkey . $timestamp
        );

        $response = Http::withToken($this->accessToken())
            ->acceptJson()
            ->timeout(30)
            ->post(
                $this->baseUrl() . '/mpesa/stkpush/v1/processrequest',
                [
                    'BusinessShortCode' => $shortcode,
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'TransactionType' => config(
                        'services.mpesa.transaction_type'
                    ),
                    'Amount' => $amount,
                    'PartyA' => $phone,
                    'PartyB' => $tillNumber,
                    'PhoneNumber' => $phone,
                    'CallBackURL' => $callbackUrl,
                    'AccountReference' => substr(
                        $accountReference,
                        0,
                        12
                    ),
                    'TransactionDesc' => 'Plumbing shop purchase',
                ]
            );

        $response->throw();

        $data = $response->json();

        if (
            empty($data['CheckoutRequestID']) ||
            empty($data['MerchantRequestID'])
        ) {
            throw new RuntimeException(
                'Daraja did not return the required checkout identifiers.'
            );
        }

        return $data;
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-]/', '', $phone);

        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        if (preg_match('/^0[17]\d{8}$/', $phone)) {
            $phone = '254' . substr($phone, 1);
        }

        if (!preg_match('/^254[17]\d{8}$/', $phone)) {
            throw new RuntimeException(
                'Enter a valid Kenyan M-Pesa phone number.'
            );
        }

        return $phone;
    }
}
