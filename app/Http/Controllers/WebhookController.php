<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateConversion;
use App\Models\AffiliateReferralClick;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use App\Jobs\SendTelegramNotification;
use App\Models\WebhookEvent;
use App\Services\MidtransService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    public function __construct(
        protected MidtransService $midtrans,
        protected NotificationService $notif,
    ) {}

    /**
     * Handle incoming payment webhook from Midtrans.
     * POST /api/webhook/payment
     */
    public function handlePayment(Request $request): JsonResponse
    {
        $payload    = $request->all();
        $rawPayload = $request->getContent();
        $externalId = $payload['order_id'] ?? null;

        // 1. Validate signature
        if (! $this->midtrans->isValidSignature($payload)) {
            $this->logWebhook($externalId, $rawPayload, false, 'invalid_signature', 'Signature mismatch');

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // 2. Idempotency — jika payment dengan external_id sudah ada, skip
        if ($externalId && Payment::where('external_id', $externalId)->exists()) {
            $this->logWebhook($externalId, $rawPayload, true, 'duplicate');

            return response()->json(['message' => 'Already processed']);
        }

        $transactionStatus = $payload['transaction_status'] ?? '';

        // 3. Hanya proses jika settlement
        if ($transactionStatus !== 'settlement') {
            $this->logWebhook($externalId, $rawPayload, true, 'processed', 'Non-settlement, skipped');

            return response()->json(['message' => 'Skipped: ' . $transactionStatus]);
        }

        try {
            DB::transaction(function () use ($payload, $rawPayload, $externalId, $request) {

                $existingOrder = Order::where('order_number', $externalId)->first();
                $grossAmount   = (float) ($payload['gross_amount'] ?? 0);

                if ($existingOrder) {
                    // ── Case A: Order created by CheckoutController ──────────
                    // Just update payment_status; Order model observer fires notification
                    $existingOrder->update([
                        'payment_status' => 'paid',
                        'paid_at'        => now(),
                    ]);
                    $order = $existingOrder->fresh();

                    // Record payment
                    Payment::create([
                        'order_id'            => $order->id,
                        'gateway_provider'    => 'midtrans',
                        'external_id'         => $externalId,
                        'gateway_invoice_id'  => $payload['transaction_id'] ?? null,
                        'payment_method'      => $payload['payment_type'] ?? null,
                        'amount'              => $grossAmount,
                        'status'              => 'paid',
                        'signature_valid'     => true,
                        'raw_payload'         => $rawPayload,
                        'webhook_received_at' => now(),
                        'paid_at'             => now(),
                    ]);

                    // Create affiliate commission if order came from referral
                    if ($order->affiliate_id && ! AffiliateConversion::where('order_id', $order->id)->exists()) {
                        $affiliate = Affiliate::find($order->affiliate_id);
                        if ($affiliate) {
                            $commissionRate   = $affiliate->commission_rate;
                            $commissionAmount = round($grossAmount * ($commissionRate / 100), 2);

                            $conversion = AffiliateConversion::create([
                                'affiliate_id'      => $affiliate->id,
                                'order_id'          => $order->id,
                                'referral_click_id' => $order->referral_click_id ?? null,
                                'commission_rate'   => $commissionRate,
                                'commission_amount' => $commissionAmount,
                                'is_self_referral'  => $affiliate->user_id === $order->customer_user_id,
                                'status'            => 'pending',
                            ]);

                            $affiliate->increment('total_conversions');
                            $affiliate->increment('total_commission_amount', $commissionAmount);
                            $this->notif->notifyAffiliateCommission($conversion);
                        }
                    }


                } else {
                    // ── Case B: Order does NOT exist (direct API / testing) ──
                    $customer = $this->resolveCustomer($payload);

                    $affiliate     = null;
                    $referralClick = null;
                    $affiliateRef  = $request->cookie('affiliate_ref');

                    if ($affiliateRef) {
                        $affiliate = Affiliate::where('referral_code', $affiliateRef)->first();
                        if ($affiliate) {
                            $referralClick = AffiliateReferralClick::where('affiliate_id', $affiliate->id)
                                ->where('is_attributed', false)
                                ->latest('created_at')
                                ->first();
                        }
                    }

                    $order = Order::create([
                        'order_number'     => $externalId,
                        'customer_user_id' => $customer->id,
                        'affiliate_id'     => $affiliate?->id,
                        'referral_click_id'=> $referralClick?->id,
                        'subtotal_amount'  => $grossAmount,
                        'discount_amount'  => 0,
                        'total_amount'     => $grossAmount,
                        'currency'         => 'IDR',
                        'order_status'     => 'pending',
                        'payment_status'   => 'paid',
                        'customer_name'    => $payload['customer_details']['first_name'] ?? null,
                        'customer_phone'   => $payload['customer_details']['phone'] ?? null,
                        'paid_at'          => now(),
                    ]);

                    foreach ($payload['item_details'] ?? [] as $item) {
                        OrderItem::create([
                            'order_id'              => $order->id,
                            'product_id'            => null,
                            'product_name_snapshot' => $item['name'] ?? 'Unknown Product',
                            'qty'                   => (int) ($item['quantity'] ?? 1),
                            'unit_price'            => (float) ($item['price'] ?? 0),
                            'line_total'            => (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 1),
                        ]);
                    }

                    Payment::create([
                        'order_id'            => $order->id,
                        'gateway_provider'    => 'midtrans',
                        'external_id'         => $externalId,
                        'gateway_invoice_id'  => $payload['transaction_id'] ?? null,
                        'payment_method'      => $payload['payment_type'] ?? null,
                        'amount'              => $grossAmount,
                        'status'              => 'paid',
                        'signature_valid'     => true,
                        'raw_payload'         => $rawPayload,
                        'webhook_received_at' => now(),
                        'paid_at'             => now(),
                    ]);

                    if ($referralClick) {
                        $referralClick->update(['is_attributed' => true]);
                    }

                    if ($affiliate) {
                        $commissionRate   = $affiliate->commission_rate;
                        $commissionAmount = round($grossAmount * ($commissionRate / 100), 2);

                        $conversion = AffiliateConversion::create([
                            'affiliate_id'      => $affiliate->id,
                            'order_id'          => $order->id,
                            'referral_click_id' => $referralClick?->id,
                            'commission_rate'   => $commissionRate,
                            'commission_amount' => $commissionAmount,
                            'is_self_referral'  => $affiliate->user_id === $customer->id,
                            'status'            => 'pending',
                        ]);

                        $affiliate->increment('total_conversions');
                        $affiliate->increment('total_commission_amount', $commissionAmount);

                        // Notify affiliate about their new commission via Telegram
                        $this->notif->notifyAffiliateCommission($conversion);
                    }

                    // Trigger notification manually for case B (observer already fired for case A)
                    $order->load('customer');
                    $this->notif->notifyOrderStatus($order, 'payment.confirmed');
                }

                $this->logWebhook($externalId, $rawPayload, true, 'processed');
            });

            return response()->json(['message' => 'OK']);
        } catch (\Throwable $e) {
            Log::error('[WebhookController] Failed to process payment', [
                'external_id' => $externalId,
                'error'       => $e->getMessage(),
            ]);

            $this->logWebhook($externalId, $rawPayload, true, 'failed', $e->getMessage());

            return response()->json(['message' => 'Server error'], 500);
        }
    }

    /**
     * Resolve atau buat customer User dari payload Midtrans.
     */
    private function resolveCustomer(array $payload): User
    {
        $email = $payload['customer_details']['email'] ?? null;
        $name  = trim(
            ($payload['customer_details']['first_name'] ?? '') . ' ' .
            ($payload['customer_details']['last_name'] ?? '')
        ) ?: 'Customer';

        if ($email) {
            return User::firstOrCreate(
                ['email' => $email],
                [
                    'name'          => $name,
                    'password_hash' => bcrypt(Str::random(16)),
                    'role'          => 'customer',
                    'is_active'     => true,
                ]
            );
        }

        // Fallback: buat guest user dengan email unik
        return User::create([
            'name'          => $name ?: 'Guest',
            'email'         => 'guest_' . Str::uuid() . '@midtrans.local',
            'password_hash' => bcrypt(Str::random(16)),
            'role'          => 'customer',
            'is_active'     => true,
        ]);
    }

    /**
     * Catat webhook event ke tabel webhook_events.
     */
    private function logWebhook(
        ?string $externalId,
        string $rawPayload,
        bool $signatureValid,
        string $processStatus,
        ?string $errorMessage = null
    ): void {
        try {
            WebhookEvent::create([
                'source'           => 'midtrans',
                'external_id'      => $externalId,
                'payload_hash'     => hash('sha256', $rawPayload),
                'signature_valid'  => $signatureValid,
                'process_status'   => $processStatus,
                'error_message'    => $errorMessage,
                'raw_payload'      => $rawPayload,
                'received_at'      => now(),
                'processed_at'     => in_array($processStatus, ['processed', 'duplicate', 'invalid_signature'])
                                        ? now()
                                        : null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('[WebhookController] Failed to log webhook event: ' . $e->getMessage());
        }
    }
}
