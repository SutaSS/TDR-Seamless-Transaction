<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * Handle incoming payment webhook from Midtrans.
     *
     * POST /api/webhook/payment
     *
     * TODO [PHASE 1 - Andika]: Implementasi sesuai TASK A2
     *
     * Flow:
     * 1. Validate Midtrans signature
     * 2. If invalid → return 401
     * 3. If order external_id already exists → return success (idempotent)
     * 4. If transaction_status = 'settlement':
     *    - Create order
     *    - Create order_items
     *    - Attach affiliate jika referral cookie tersedia
     *    - Insert affiliate_conversion
     *    - Insert notification (status = queued)
     * 5. Return JSON success
     *
     * ⚠️ DO NOT send Telegram di sini
     * ⚠️ DO NOT implement retry di sini
     */
    public function handlePayment(Request $request)
    {
        // TODO [PHASE 1 - Andika]: Implementasi logic di sini
    }
}
