<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name'  => 'Admin TDR',
            'email' => 'admin@tdr.test',
            'role'  => 'admin',
        ]);

        // TODO [PHASE 1 - Andika]: Jalankan AffiliateSeeder setelah migrations selesai
        $this->call([
            AffiliateSeeder::class,
        ]);
    }
}
