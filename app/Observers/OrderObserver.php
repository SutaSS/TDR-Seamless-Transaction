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
        // NOTE: payment.confirmed is sent explicitly in OrderService::verifyPayment()
        // to ensure it only fires after the DB transaction commits.
        // Do NOT send it here to avoid double notifications.

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
