<?php

namespace App\Services;

use App\Jobs\SendTelegramNotification;
use App\Models\NotificationLog;
use App\Models\Order;
use Illuminate\Support\Carbon;

class NotificationService
{
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

        $notification = NotificationLog::create([
            'user_id'         => $user->id,
            'order_id'        => $order->id,
            'message_type'    => $event,
            'channel'         => 'telegram',
            'recipient'       => $user->telegram_chat_id ?? '',
            'message_content' => $message,
            'status'          => 'queued',
        ]);

        SendTelegramNotification::dispatch($notification->id);
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
                "Pesanan *{$ordNum}* telah dikirim via *{$order->shipping_courier}*.\n" .
                ($order->shipping_tracking_number
                    ? "Nomor Resi: `{$order->shipping_tracking_number}`\n\n"
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
     * Notify an affiliate when their account is approved by admin.
     */
    public function notifyAffiliateApproved(\App\Models\AffiliateProfile $profile): void
    {
        $user   = $profile->user;
        $chatId = $user?->telegram_chat_id;

        if (! $chatId) {
            return;
        }

        $date    = \Illuminate\Support\Carbon::now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i');
        $name    = $user->name;
        $code    = $profile->referral_code;
        $rate    = $profile->commission_rate;

        $message = "*TDR-HPZ Affiliate* ✅\n\n"
                 . "Selamat *{$name}*!\n\n"
                 . "Akun affiliate Anda telah *disetujui*.\n\n"
                 . "🔑 Kode Referral: `{$code}`\n"
                 . "💰 Komisi: *{$rate}%* per transaksi\n"
                 . "⏰ Disetujui: {$date}\n\n"
                 . "Mulai bagikan link referral Anda dan raih komisi! 🚀";

        $notification = NotificationLog::create([
            'user_id'         => $user->id,
            'order_id'        => null,
            'message_type'    => 'affiliate.approved',
            'channel'         => 'telegram',
            'recipient'       => $chatId,
            'message_content' => $message,
            'status'          => 'queued',
        ]);

        SendTelegramNotification::dispatch($notification->id);
    }

    /**
     * Notify an affiliate when they earn a commission.
     */
    public function notifyAffiliateCommission(\App\Models\AffiliateCommission $commission): void
    {
        $affiliate = $commission->affiliate;   // BelongsTo User
        $chatId    = $affiliate?->telegram_chat_id;

        if (! $chatId) {
            return;
        }

        $order      = $commission->order;
        $affName    = $affiliate->name;
        $ordNum     = $order?->order_number ?? '—';
        $amount     = 'Rp ' . number_format((float) $commission->amount, 0, ',', '.');
        $rate       = $commission->commission_rate;
        $date       = \Illuminate\Support\Carbon::now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i');

        $message = "*TDR-HPZ Affiliate* 🎉\n\n"
                 . "*{$affName}*, kamu baru saja mendapatkan komisi!\n\n"
                 . "📦 Order: *{$ordNum}*\n"
                 . "💰 Komisi ({$rate}%): *{$amount}*\n"
                 . "⏰ Waktu: {$date}\n\n"
                 . "Komisi akan masuk ke saldo setelah pesanan selesai (completed). Keep sharing! 🚀";

        // Simpan log dan kirim via job
        $notification = NotificationLog::create([
            'user_id'         => $affiliate->id,
            'order_id'        => $commission->order_id,
            'message_type'    => 'affiliate.commission',
            'channel'         => 'telegram',
            'recipient'       => $chatId,
            'message_content' => $message,
            'status'          => 'queued',
        ]);

        SendTelegramNotification::dispatch($notification->id);
    }
}
