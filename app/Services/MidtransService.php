<?php

namespace App\Services;

class MidtransService
{
    protected string $serverKey;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key', '');
    }

    /**
     * Validate Midtrans webhook signature.
     *
     * Formula: SHA512(order_id + status_code + gross_amount + server_key)
     */
    public function isValidSignature(array $payload): bool
    {
        $orderId     = $payload['order_id'] ?? '';
        $statusCode  = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signature   = $payload['signature_key'] ?? '';

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        return hash_equals($expected, $signature);
    }
}
