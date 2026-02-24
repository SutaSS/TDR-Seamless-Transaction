<?php

namespace Database\Seeders;

use App\Models\Affiliate;
use App\Models\AffiliateReferralClick;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AffiliateSeeder extends Seeder
{
    public function run(): void
    {
        // --- 3 Sample Products (spare part motor TDR HPZ) ---
        $products = [
            [
                'sku'         => 'TDR-CVT-001',
                'name'        => 'TDR Package Zozz for Beat/Scoopy FI ESP with CVT Belt Green Line',
                'description' => 'Paket CVT akselerasi untuk Honda Beat/Scoopy FI ESP, termasuk CVT Belt Green Line.',
                'price'       => 1300000,
                'commission_rate' => 10.00,
                'stock'       => 30,
                'is_active'   => true,
            ],
            [
                'sku'         => 'KOSO-CVT-002',
                'name'        => 'Koso CVT Crankcase Carbon Fiber Pattern for All New N-Max/Aerox',
                'description' => 'Cover CVT motif carbon fiber untuk Yamaha All New N-Max dan Aerox.',
                'price'       => 1680000,
                'commission_rate' => 10.00,
                'stock'       => 15,
                'is_active'   => true,
            ],
            [
                'sku'         => 'RPD-BRK-003',
                'name'        => 'RPD Braking Package for Aerox / All New Aerox with Master Brake Genesis',
                'description' => 'Paket pengereman lengkap untuk Yamaha Aerox, termasuk Master Brake Genesis & Rear View Mirror.',
                'price'       => 3120000,
                'commission_rate' => 10.00,
                'stock'       => 10,
                'is_active'   => true,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['sku' => $product['sku']], $product);
        }

        // --- Affiliate 1: referral_code = TEST123 (untuk demo & testing) ---
        $user1 = User::firstOrCreate(
            ['email' => 'affiliate1@tdr.test'],
            [
                'name'          => 'Budi Santoso',
                'password_hash' => Hash::make('password'),
                'role'          => 'affiliate',
                'is_active'     => true,
                'telegram_chat_id' => '123456789',
            ]
        );

        $affiliate1 = Affiliate::firstOrCreate(
            ['user_id' => $user1->id],
            [
                'referral_code'          => 'TEST123',
                'status'                 => 'approved',
                'commission_rate'        => 10.00,
                'payout_method'          => 'bank_transfer',
                'payout_account_name'    => 'Budi Santoso',
                'payout_account_number'  => '1234567890',
                'total_clicks'           => 0,
                'total_conversions'      => 0,
                'total_commission_amount'=> 0,
            ]
        );

        // --- Affiliate 2 ---
        $user2 = User::firstOrCreate(
            ['email' => 'affiliate2@tdr.test'],
            [
                'name'             => 'Siti Rahayu',
                'password_hash'    => Hash::make('password'),
                'role'             => 'affiliate',
                'is_active'        => true,
                'telegram_chat_id' => '987654321',
            ]
        );

        $affiliate2 = Affiliate::firstOrCreate(
            ['user_id' => $user2->id],
            [
                'referral_code'          => 'SITI2024',
                'status'                 => 'approved',
                'commission_rate'        => 10.00,
                'payout_method'          => 'bank_transfer',
                'payout_account_name'    => 'Siti Rahayu',
                'payout_account_number'  => '0987654321',
                'total_clicks'           => 0,
                'total_conversions'      => 0,
                'total_commission_amount'=> 0,
            ]
        );

        // --- Sample Referral Clicks ---
        $clicks = [
            [
                'affiliate_id'            => $affiliate1->id,
                'referral_code_snapshot'  => 'TEST123',
                'anonymized_ip'           => '192.168.1.xxx',
                'user_agent'              => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'landing_url'             => 'http://localhost/?ref=TEST123',
                'is_attributed'           => false,
                'expires_at'              => now()->addDays(30),
            ],
            [
                'affiliate_id'            => $affiliate1->id,
                'referral_code_snapshot'  => 'TEST123',
                'anonymized_ip'           => '10.0.0.xxx',
                'user_agent'              => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0)',
                'landing_url'             => 'http://localhost/?ref=TEST123',
                'is_attributed'           => true,
                'expires_at'              => now()->addDays(30),
            ],
            [
                'affiliate_id'            => $affiliate2->id,
                'referral_code_snapshot'  => 'SITI2024',
                'anonymized_ip'           => '172.16.0.xxx',
                'user_agent'              => 'Mozilla/5.0 (Android 13; Mobile)',
                'landing_url'             => 'http://localhost/?ref=SITI2024',
                'is_attributed'           => false,
                'expires_at'              => now()->addDays(30),
            ],
        ];

        foreach ($clicks as $click) {
            AffiliateReferralClick::create($click);
        }

        // Update total_clicks
        $affiliate1->update(['total_clicks' => 2]);
        $affiliate2->update(['total_clicks' => 1]);

        $this->command->info('✅ AffiliateSeeder: 2 affiliates, 3 products, 3 referral clicks seeded.');
    }
}
