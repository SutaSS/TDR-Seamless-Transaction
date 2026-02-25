<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\NotificationService;

class OrderObserver
{
    public function __construct(protected NotificationService $notif) {}

    /**
     * Called after an order is updated. Detect status transitions and fire notifications.
     */
    public function updated(Order $order): void
    {
        // payment_verified_at berubah dari null → terisi → berarti baru dibayar
        if ($order->wasChanged('payment_verified_at') && $order->payment_verified_at !== null) {
            $this->notif->notifyOrderStatus($order, 'payment.confirmed');
        }

        // status berubah → kirim notifikasi event yang sesuai
        if ($order->wasChanged('status')) {
            $event = match ($order->status) {
                'processing' => 'order.processing',
                'shipped'    => 'order.shipped',
                'completed'  => 'order.delivered',
                'cancelled'  => 'order.cancelled',
                default      => null,
            };

            if ($event) {
                $this->notif->notifyOrderStatus($order, $event);
            }
        }
    }
}
