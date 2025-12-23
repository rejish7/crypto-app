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
            'balance' => 100000.00, 
        ]);

        $seller = User::factory()->create([
            'name' => 'Test Seller',
            'email' => 'seller@example.com',
            'balance' => 50000.00, 
        ]);

        // seller  BTC and ETH
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
        $this->command->info('Buyer: buyer@example.com | Balance: $100,000');
        $this->command->info('Seller: seller@example.com | Balance: $50,000 | BTC: 1.0 | ETH: 10.0');
    }
}
