<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AffiliateSeeder extends Seeder
{
    /**
     * TODO [PHASE 1 - Andika]: Seed data demo sesuai TASK A3
     *
     * Requirements:
     * - 2 affiliates (dengan user masing-masing)
     * - 1 affiliate dengan referral_code = 'TEST123' (untuk demo/testing)
     * - 3 sample products (dummy only)
     * - Sample referral clicks
     */
    public function run(): void
    {
        // TODO [PHASE 1 - Andika]: Seed 2 affiliate users + affiliate records
        // Contoh:
        // $user1 = User::create([...]);
        // Affiliate::create(['user_id' => $user1->id, 'referral_code' => 'TEST123', ...]);

        // TODO [PHASE 1 - Andika]: Seed 3 sample products
        // Product::create(['name' => '...', 'price' => ..., 'sku' => '...']);

        // TODO [PHASE 1 - Andika]: Seed sample referral clicks
        // AffiliateReferralClick::create([...]);
    }
}
