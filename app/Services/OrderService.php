<?php

namespace App\Services;

use App\Models\AffiliateProfile;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\TrackingLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected MidtransService    $midtrans,
        protected AffiliateService   $affiliate,
        protected NotificationService $notification,
    ) {}

    /**
     * Create a new order, attach items, and obtain a Midtrans Snap token.
     *
     * @param array $data   Validated payload from CreateOrderRequest
     * @param int   $customerId
     */
    public function createOrder(array $data, int $customerId): Order
    {
        return DB::transaction(function () use ($data, $customerId) {
            $product  = Product::findOrFail($data['product_id']);
            $qty      = (int) ($data['quantity'] ?? 1);
            $subtotal = $product->price * $qty;

            // Resolve affiliate if referral cookie is present
            $affiliateId      = null;
            $commissionRate   = 0;
            $commissionAmount = 0;

            if (! empty($data['affiliate_code'])) {
                $affiliateProfile = AffiliateProfile::where('referral_code', $data['affiliate_code'])
                    ->where('status', 'active')
                    ->first();

                if ($affiliateProfile) {
                    $affiliateId      = $affiliateProfile->user_id;
                    $commissionRate   = $affiliateProfile->commission_rate;
                    $commissionAmount = round($subtotal * ($commissionRate / 100), 2);
                }
            }

            $orderNumber = 'TDR-' . strtoupper(Str::random(8));

            /** @var Order $order */
            $order = Order::create([
                'order_number'     => $orderNumber,
                'customer_id'      => $customerId,
                'affiliate_id'     => $affiliateId,
                'subtotal'         => $subtotal,
                'commission_amount'=> $commissionAmount,
                'total_amount'     => $subtotal,
                'status'           => 'pending',
                'payment_method'   => $data['payment_method'] ?? null,
                'shipping_address' => $data['shipping_address'],
                'shipping_courier' => $data['shipping_courier'],
                'notes'            => $data['notes'] ?? null,
            ]);

            OrderItem::create([
                'order_id'      => $order->id,
                'product_id'    => $product->id,
                'product_name'  => $product->name,
                'product_price' => $product->price,
                'quantity'      => $qty,
                'subtotal'      => $subtotal,
            ]);

            // Decrement product stock
            if ($product->stock !== null) {
                $product->decrement('stock', $qty);
            }

            // Build Midtrans Snap token
            $snapUrl = $this->midtrans->createSnapToken([
                'transaction_details' => [
                    'order_id'     => $order->order_number,
                    'gross_amount' => (int) $subtotal,
                ],
                'customer_details' => [
                    'first_name' => $order->customer?->name ?? 'Customer',
                    'email'      => $order->customer?->email ?? '',
                ],
                'item_details' => [[
                    'id'       => $product->id,
                    'price'    => (int) $product->price,
                    'quantity' => $qty,
                    'name'     => mb_substr($product->name, 0, 50),
                ]],
            ]);

            $order->update(['midtrans_snap_token' => $snapUrl]);

            TrackingLog::create([
                'order_id'     => $order->id,
                'status_title' => 'Pesanan Dibuat',
                'description'  => 'Pesanan berhasil dibuat, menunggu pembayaran.',
            ]);

            return $order->fresh();
        });
    }

    /**
     * Confirm payment from Midtrans webhook, transition order to verified.
     */
    public function verifyPayment(Order $order, string $transactionId): void
    {
        DB::transaction(function () use ($order, $transactionId) {
            $order->update([
                'status'                  => 'verified',
                'midtrans_transaction_id' => $transactionId,
                'payment_verified_at'     => now(),
            ]);

            TrackingLog::create([
                'order_id'     => $order->id,
                'status_title' => 'Pembayaran Dikonfirmasi',
                'description'  => 'Pembayaran telah diverifikasi via Midtrans.',
            ]);

            // Immediately earn affiliate commission on payment verification
            if ($order->affiliate_id) {
                $commission = $this->affiliate->recordCommission($order);
                if ($commission) {
                    $this->affiliate->earnCommission($commission);
                }
            }
        });

        $this->notification->notifyOrderStatus($order->fresh(), 'payment.confirmed');
    }
}
