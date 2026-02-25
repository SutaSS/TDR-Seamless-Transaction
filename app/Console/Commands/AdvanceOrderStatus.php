<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Auto-advance order statuses based on elapsed time.
 *
 * Timing (adjustable):
 *   pending     →  processing : 1 min  after payment_verified_at
 *   processing  →  shipped    : 5 min  after updated_at
 *   shipped     →  completed  : 15 min after updated_at
 *
 * Catatan: status valid di tabel orders: pending, verified, processing, shipped, completed, cancelled.
 */
class AdvanceOrderStatus extends Command
{
    protected $signature   = 'orders:advance-status';
    protected $description = 'Auto-advance order statuses based on elapsed time after payment';

    public function handle(): int
    {
        $now     = Carbon::now();
        $advanced = 0;

        // ── 1. pending → processing (1 menit setelah payment_verified_at) ──
        Order::whereNotNull('payment_verified_at')
            ->where('status', 'pending')
            ->where('payment_verified_at', '<=', $now->copy()->subMinute())
            ->each(function (Order $order) use (&$advanced) {
                $order->update(['status' => 'processing']);
                $advanced++;
                $this->line("  ✅ #{$order->order_number}: pending → processing");
            });

        // ── 2. processing → shipped (5 menit setelah updated_at) ───────────
        Order::where('status', 'processing')
            ->where('updated_at', '<=', $now->copy()->subMinutes(5))
            ->each(function (Order $order) use (&$advanced) {
                $order->update(['status' => 'shipped', 'shipped_at' => now()]);
                $advanced++;
                $this->line("  🚚 #{$order->order_number}: processing → shipped");
            });

        // ── 3. shipped → completed (15 menit setelah shipped_at) ────────────
        Order::where('status', 'shipped')
            ->where(function ($q) use ($now) {
                $q->whereNotNull('shipped_at')
                  ->where('shipped_at', '<=', $now->copy()->subMinutes(15));
            })
            ->each(function (Order $order) use (&$advanced) {
                $order->update(['status' => 'completed', 'completed_at' => now()]);
                $advanced++;
                $this->line("  🎉 #{$order->order_number}: shipped → completed");
            });

        if ($advanced === 0) {
            $this->line('  No orders to advance.');
        } else {
            $this->info("  Advanced {$advanced} order(s).");
        }

        return self::SUCCESS;
    }
}
