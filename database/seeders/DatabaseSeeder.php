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

        User::firstOrCreate(
            ['email' => 'admin@tdr.test'],
            [
                'name'          => 'Admin TDR',
                'password_hash' => bcrypt('admin123'),
                'role'          => 'admin',
                'is_active'     => true,
            ]
        );

        $this->call([
            AffiliateSeeder::class,
            NotificationTemplateSeeder::class,
        ]);
    }
}
