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
        // payment_status changed to 'paid' → notify payment confirmed
        if ($order->wasChanged('payment_status') && $order->payment_status === 'paid') {
            $this->notif->notifyOrderStatus($order, 'payment.confirmed');
        }

        // order_status changed → notify relevant event; also stamp status_changed_at
        if ($order->wasChanged('order_status')) {
            // Stamp time so the scheduler knows when status changed
            $order->timestamps = false;
            $order->updateQuietly(['status_changed_at' => now()]);
            $order->timestamps = true;

            $event = match ($order->order_status) {
                'processing' => 'order.processing',
                'shipped'    => 'order.shipped',
                'delivered'  => 'order.delivered',
                'cancelled'  => 'order.cancelled',
                default      => null,
            };

            if ($event) {
                $this->notif->notifyOrderStatus($order, $event);
            }
        }
    }
}
