<?php

namespace Database\Seeders;

use App\Models\Asset;
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
        // Create test users with balance
        $buyer = User::factory()->create([
            'name' => 'Test Buyer',
            'email' => 'buyer@example.com',
            'password' => bcrypt('password'),
            'balance' => 100000.00,
        ]);

        $seller = User::factory()->create([
            'name' => 'Test Seller',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
            'balance' => 50000.00,
        ]);

        // Give seller BTC and ETH
        Asset::create([
            'user_id' => $seller->id,
            'symbol' => 'BTC',
            'amount' => '1.00000000',
            'locked_amount' => '0.00000000',
        ]);

        Asset::create([
            'user_id' => $seller->id,
            'symbol' => 'ETH',
            'amount' => '10.00000000',
            'locked_amount' => '0.00000000',
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('===== TEST ACCOUNTS =====');
        $this->command->info('Buyer Account:');
        $this->command->info('  Email: buyer@example.com');
        $this->command->info('  Password: password');
        $this->command->info('  Balance: $100,000');
        $this->command->info('');
        $this->command->info('Seller Account:');
        $this->command->info('  Email: seller@example.com');
        $this->command->info('  Password: password');
        $this->command->info('  Balance: $50,000');
        $this->command->info('  Assets: 1.0 BTC, 10.0 ETH');
        $this->command->info('=========================');
    }
}
