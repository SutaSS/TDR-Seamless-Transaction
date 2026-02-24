<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $templates = [
            [
                'template_key' => 'order.paid',
                'event_type'   => 'order.paid',
                'title'        => 'Pesanan Dibayar',
                'channel'      => 'telegram',
                'body_template'=> "*Pesanan Dibayar!* 🎉\n\nNomor: #{order_number}\nTotal: {total_amount}\nWaktu: {paid_at}\n\nTerima kasih sudah berbelanja di TDR HPZ.",
                'is_active'    => true,
            ],
            [
                'template_key' => 'order.shipped',
                'event_type'   => 'order.shipped',
                'title'        => 'Pesanan Dikirim',
                'channel'      => 'telegram',
                'body_template'=> "*Pesanan Dikirim!* 🚚\n\nNomor: #{order_number}\nEkspedisi: {shipping_provider}\nNo. Resi: {tracking_number}\n\nCek status pengiriman secara berkala.",
                'is_active'    => true,
            ],
            [
                'template_key' => 'order.delivered',
                'event_type'   => 'order.delivered',
                'title'        => 'Pesanan Tiba',
                'channel'      => 'telegram',
                'body_template'=> "*Pesanan Tiba!* 📦\n\nNomor: #{order_number}\n\nTerima kasih sudah berbelanja di TDR HPZ.\nJangan lupa kasih rating ya! ⭐",
                'is_active'    => true,
            ],
            [
                'template_key' => 'commission.earned',
                'event_type'   => 'commission.earned',
                'title'        => 'Komisi Diterima',
                'channel'      => 'telegram',
                'body_template'=> "*Komisi Masuk!* 💰\n\nOrder: #{order_number}\nKomisi: {commission_amount} ({commission_rate}%)\n\nTerus bagikan link Anda untuk komisi lebih banyak!",
                'is_active'    => true,
            ],
        ];

        foreach ($templates as $tpl) {
            NotificationTemplate::updateOrCreate(
                ['template_key' => $tpl['template_key']],
                array_merge($tpl, ['created_by_user_id' => $admin?->id])
            );
        }

        $this->command->info('✅ NotificationTemplateSeeder: ' . count($templates) . ' templates seeded.');
    }
}
