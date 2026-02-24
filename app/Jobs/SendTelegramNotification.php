<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class SendTelegramNotification implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    /**
     * TODO [PHASE 3 - Syahru]: Implementasi sesuai TASK S2
     *
     * Logic:
     * 1. Fetch pending notification dari table notifications
     * 2. Kirim via TelegramService::sendMessage()
     * 3. Jika success → status = 'sent'
     * 4. Jika failed:
     *    - retry_count++
     *    - delay: 1→30s, 2→60s, 3→120s
     *    - max retry = 3
     *    - Jika > 3 → status = 'failed'
     *
     * Queue driver: database OR sync only (NO Redis/Kafka)
     */

    // TODO [PHASE 3 - Syahru]: Tambahkan property notification yang akan dikirim
    // public Notification $notification;

    public function __construct()
    {
        // TODO [PHASE 3 - Syahru]: Inject Notification model/ID ke job
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // TODO [PHASE 3 - Syahru]: Implementasi logic pengiriman di sini
    }

    /**
     * Determine backoff time based on retry count.
     *
     * TODO [PHASE 3 - Syahru]: Return array [30, 60, 120]
     */
    public function backoff(): array
    {
        // TODO [PHASE 3 - Syahru]: return [30, 60, 120];
        return [];
    }
}
