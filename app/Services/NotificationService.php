<?php

namespace App\Services;

use App\Jobs\SendTelegramNotification;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Support\Carbon;

class NotificationService
{
    public function __construct(private TelegramService $telegram) {}

    /**
     * Fire a Telegram notification for an order event.
     * Creates a Notification record then dispatches the job.
     */
    public function notifyOrderStatus(Order $order, string $event): void
    {
        $user = $order->customer;

        if (! $user) {
            return;
        }

        $message = $this->buildMessage($order, $event);

        if (! $message) {
            return;
        }

        $notification = Notification::create([
            'user_id'                    => $user->id,
            'order_id'                   => $order->id,
            'event_type'                 => $event,
            'channel'                    => 'telegram',
            'recipient_chat_id_snapshot' => $user->telegram_chat_id,
            'message_body'               => $message,
            'status'                     => 'queued',
        ]);

        SendTelegramNotification::dispatch($notification->id);
    }

    /**
     * Fire a Telegram notification SYNCHRONOUSLY (no queue worker needed).
     * Used by console commands / schedulers where queue worker may not be running.
     */
    public function notifyOrderStatusSync(Order $order, string $event): void
    {
        $user = $order->customer;

        if (! $user || ! $user->telegram_chat_id) {
            return;
        }

        $message = $this->buildMessage($order, $event);
        if (! $message) {
            return;
        }

        $notification = Notification::create([
            'user_id'                    => $user->id,
            'order_id'                   => $order->id,
            'event_type'                 => $event,
            'channel'                    => 'telegram',
            'recipient_chat_id_snapshot' => $user->telegram_chat_id,
            'message_body'               => $message,
            'status'                     => 'queued',
        ]);

        $ok = $this->telegram->sendMessage($user->telegram_chat_id, $message);

        $notification->update([
            'status'    => $ok ? 'sent' : 'failed',
            'sent_at'   => $ok ? now() : null,
            'last_error'=> $ok ? null : 'Sync send failed',
        ]);
    }

    /**
     * Build the Telegram message text based on event type.
     */
    private function buildMessage(Order $order, string $event): ?string
    {
        $customer = $order->customer;
        $name     = $customer?->name ?? 'Pelanggan';
        $ordNum   = $order->order_number;
        $total    = 'Rp ' . number_format((float) $order->total_amount, 0, ',', '.');
        $date     = Carbon::now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i');

        return match ($event) {
            'payment.confirmed' =>
                "*TDR-HPZ Store*, [{$date}]\n" .
                "✅ *Pembayaran Dikonfirmasi*\n\n" .
                "Halo {$name},\n\n" .
                "Pembayaran untuk pesanan *{$ordNum}* telah kami terima.\n" .
                "Total: *{$total}*\n\n" .
                "Pesanan Anda sedang kami proses. Kami akan mengirimkan notifikasi saat barang dikirim.\n\n" .
                "Terima kasih telah berbelanja di store.tdr-hpz.com! 🚀",

            'order.processing' =>
                "*TDR-HPZ Store*, [{$date}]\n" .
                "⚙️ *Pesanan Diproses*\n\n" .
                "Halo {$name},\n\n" .
                "Pesanan *{$ordNum}* sedang dalam proses pengemasan.\n\n" .
                "Kami akan segera mengirimkan pesanan Anda. Nantikan informasi pengiriman selanjutnya!",

            'order.shipped' =>
                "*TDR-HPZ Store*, [{$date}]\n" .
                "📦 *Pesanan Dikirim*\n\n" .
                "Halo {$name},\n\n" .
                "Pesanan *{$ordNum}* telah dikirim via *{$order->shipping_provider}*.\n" .
                ($order->tracking_number
                    ? "Nomor Resi: `{$order->tracking_number}`\n\n"
                    : "\n") .
                "Silakan pantau pengiriman Anda. Terima kasih! 🙏",

            'order.delivered' =>
                "*TDR-HPZ Store*, [{$date}]\n" .
                "🎉 *Pesanan Selesai*\n\n" .
                "Halo {$name},\n\n" .
                "Pesanan *{$ordNum}* telah selesai.\n\n" .
                "Terima kasih telah berbelanja di store.tdr-hpz.com! 🚀",

            'order.cancelled' =>
                "*TDR-HPZ Store*, [{$date}]\n" .
                "❌ *Pesanan Dibatalkan*\n\n" .
                "Halo {$name},\n\n" .
                "Pesanan *{$ordNum}* telah dibatalkan.\n\n" .
                "Jika ada pertanyaan, silakan hubungi kami.",

            default => null,
        };
    }

    /**
     * Notify an affiliate when they earn a commission.
     */
    public function notifyAffiliateCommission(\App\Models\AffiliateConversion $conversion): void
    {
        $affiliate = $conversion->affiliate()->with('user')->first();
        $chatId    = $affiliate?->user?->telegram_chat_id;

        if (! $chatId) {
            return;
        }

        $order      = $conversion->order;
        $affName    = $affiliate->user->name;
        $ordNum     = $order?->order_number ?? '—';
        $commission = 'Rp ' . number_format((float) $conversion->commission_amount, 0, ',', '.');
        $rate       = $conversion->commission_rate;
        $date       = \Illuminate\Support\Carbon::now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i');

        $message = "*TDR-HPZ Affiliate* 🎉\n\n"
                 . "*{$affName}*, kamu baru saja mendapatkan komisi!\n\n"
                 . "📦 Order: *{$ordNum}*\n"
                 . "💰 Komisi ({$rate}%): *{$commission}*\n"
                 . "⏰ Waktu: {$date}\n\n"
                 . "Komisi akan masuk ke saldo setelah pesanan selesai (delivered). Keep sharing! 🚀";

        // Create notification record
        $notification = \App\Models\Notification::create([
            'user_id'                    => $affiliate->user_id,
            'order_id'                   => $conversion->order_id,
            'event_type'                 => 'affiliate.commission',
            'channel'                    => 'telegram',
            'recipient_chat_id_snapshot' => $chatId,
            'message_body'               => $message,
            'status'                     => 'queued',
        ]);

        \App\Jobs\SendTelegramNotification::dispatch($notification->id);
    }

    /**
     * Notify affiliate when their commission is CONFIRMED (order delivered).
     * Commission status moves pending → approved = siap dicairkan.
     */
    public function notifyAffiliateCommissionConfirmed(\App\Models\AffiliateConversion $conversion): void
    {
        $affiliate = $conversion->affiliate()->with('user')->first();
        $chatId    = $affiliate?->user?->telegram_chat_id;

        if (! $chatId) {
            return;
        }

        $order      = $conversion->order;
        $affName    = $affiliate->user->name;
        $ordNum     = $order?->order_number ?? '—';
        $commission = 'Rp ' . number_format((float) $conversion->commission_amount, 0, ',', '.');
        $date       = \Illuminate\Support\Carbon::now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i');

        $message = "*TDR-HPZ Affiliate* ✅\n\n"
                 . "Halo *{$affName}*, komisi Anda sudah *dikonfirmasi*!\n\n"
                 . "📦 Order: *{$ordNum}* telah diterima pembeli\n"
                 . "💰 Komisi: *{$commission}*\n"
                 . "📅 Waktu: {$date}\n\n"
                 . "Komisi ini kini *siap dicairkan*. Buka dashboard affiliate dan klik *Cairkan Komisi* untuk mengajukan pencairan. 🎉";

        $this->telegram->sendMessage($chatId, $message);
    }

    /**
     * Notify affiliate (and optionally admin) when a payout is requested.
     */
    public function notifyAffiliatePayoutRequested(\App\Models\Affiliate $affiliate, float $amount): void
    {
        $chatId  = $affiliate->user?->telegram_chat_id;
        $affName = $affiliate->user?->name ?? 'Affiliate';
        $total   = 'Rp ' . number_format($amount, 0, ',', '.');
        $method  = strtoupper($affiliate->payout_method ?? '-');
        $account = $affiliate->payout_account_number ?? '-';
        $date    = \Illuminate\Support\Carbon::now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i');

        // Notify the affiliate
        if ($chatId) {
            $msg = "*TDR-HPZ Affiliate* 💸\n\n"
                 . "Halo *{$affName}*, permintaan pencairan komisi Anda telah *diterima*!\n\n"
                 . "💰 Total pencairan: *{$total}*\n"
                 . "🏦 Metode: *{$method}*\n"
                 . "📋 Nomor: *{$account}*\n"
                 . "📅 Waktu pengajuan: {$date}\n\n"
                 . "Dana akan ditransfer dalam *1–3 hari kerja*. Terima kasih! 🙏";

            $this->telegram->sendMessage($chatId, $msg);
        }

        // Notify admin (first active admin with telegram_chat_id)
        $admin = \App\Models\User::where('role', 'admin')
            ->where('is_active', true)
            ->whereNotNull('telegram_chat_id')
            ->first();

        if ($admin?->telegram_chat_id) {
            $adminMsg = "*[Admin TDR-HPZ]* Permintaan Pencairan Komisi 💸\n\n"
                      . "Affiliate: *{$affName}*\n"
                      . "Total: *{$total}*\n"
                      . "Metode: *{$method}* — {$account}\n"
                      . "Waktu: {$date}\n\n"
                      . "Harap proses transfer secepatnya. ✅";

            $this->telegram->sendMessage($admin->telegram_chat_id, $adminMsg);
        }
    }
}
