<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Auto-advance order statuses based on elapsed time.
 *
 * Timing (adjustable):
 *   pending     →  processing : 1 min  after paid_at
 *   processing  →  shipped    : 5 min  after status_changed_at
 *   shipped     →  delivered  : 15 min after status_changed_at
 *
 * Run via scheduler every minute: Schedule::command('orders:advance-status')->everyMinute();
 */
class AdvanceOrderStatus extends Command
{
    protected $signature   = 'orders:advance-status';
    protected $description = 'Auto-advance order statuses based on elapsed time after payment';

    public function handle(): int
    {
        $now     = Carbon::now();
        $advanced = 0;

        // ── 1. pending → processing (1 min after paid_at) ───────────────────
        Order::where('payment_status', 'paid')
            ->where('order_status', 'pending')
            ->where('paid_at', '<=', $now->copy()->subMinute())
            ->each(function (Order $order) use (&$advanced) {
                $order->update(['order_status' => 'processing']);
                $advanced++;
                $this->line("  ✅ #{$order->order_number}: pending → processing");
            });

        // ── 2. processing → shipped (5 min after status_changed_at) ─────────
        Order::where('order_status', 'processing')
            ->where(function ($q) use ($now) {
                // fall back to updated_at if status_changed_at is null
                $q->where('status_changed_at', '<=', $now->copy()->subMinutes(5))
                  ->orWhere(function ($q2) use ($now) {
                      $q2->whereNull('status_changed_at')
                         ->where('updated_at', '<=', $now->copy()->subMinutes(5));
                  });
            })
            ->each(function (Order $order) use (&$advanced) {
                $order->update(['order_status' => 'shipped']);
                $advanced++;
                $this->line("  🚚 #{$order->order_number}: processing → shipped");
            });

        // ── 3. shipped → delivered (15 min after status_changed_at) ─────────
        Order::where('order_status', 'shipped')
            ->where(function ($q) use ($now) {
                $q->where('status_changed_at', '<=', $now->copy()->subMinutes(15))
                  ->orWhere(function ($q2) use ($now) {
                      $q2->whereNull('status_changed_at')
                         ->where('updated_at', '<=', $now->copy()->subMinutes(15));
                  });
            })
            ->each(function (Order $order) use (&$advanced) {
                $order->update([
                    'order_status' => 'delivered',
                    'delivered_at' => now(),
                ]);
                $advanced++;
                $this->line("  🎉 #{$order->order_number}: shipped → delivered");
            });

        if ($advanced === 0) {
            $this->line('  No orders to advance.');
        } else {
            $this->info("  Advanced {$advanced} order(s).");
        }

        return self::SUCCESS;
    }
}
