<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\BillingPeriod;
use App\Models\Role;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerRole = Role::where('name', 'customer')->first();
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$customerRole || !$adminRole) {
            $this->command->error("Roles 'customer' or 'admin' not found. Please run RoleSeeder first.");
            return;
        }

        // 1. Buat Admin User untuk login dan melihat panel
        User::firstOrCreate([
            'phone' => '08111111111'
        ], [
            'role_id' => $adminRole->id,
            'name' => 'System Admin',
            'email' => 'admin@pdam.test',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        // 2. Buat 10 Pelanggan Uji dengan data meteran
        $tariffGroups = ['R1', 'R2', 'N1', 'N2'];
        $meterSizes = ['1/2"', '3/4"', '1"'];

        for ($i = 1; $i <= 10; $i++) {
            $name = "Pelanggan Uji " . $i;
            $phone = '0812345678' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $customerNumber = 'CUS' . date('Y') . str_pad($i, 4, '0', STR_PAD_LEFT);
            $meterNumber = 'MTR' . str_pad($i, 5, '0', STR_PAD_LEFT);
            $randomTariff = $tariffGroups[array_rand($tariffGroups)];

            // Create user
            $user = User::firstOrCreate([
                'phone' => $phone
            ], [
                'role_id' => $customerRole->id,
                'name' => $name,
                'email' => "customer{$i}@test.com",
                'password' => Hash::make('password123'),
                'is_active' => $i % 9 === 0 ? false : true, // 1 user nonaktif
                'phone_verified_at' => now(),
            ]);

            // Create customer
            $customer = Customer::firstOrCreate([
                'customer_number' => $customerNumber
            ], [
                'user_id' => $user->id,
                'ktp_number' => Str::random(16),
                'address' => "Jl. Uji Coba {$i}, Blok A/B, Kota Uji",
                'tariff_group' => $randomTariff, // Dipertahankan untuk filter dan kompatibilitas lama
                'power_capacity' => 1300,
            ]);

            // Create meter dengan customer_group_code
            Meter::firstOrCreate([
                'meter_number' => $meterNumber
            ], [
                'customer_id' => $customer->id,
                'meter_type' => $i % 2 === 0 ? 'digital' : 'analog',
                'customer_group_code' => $randomTariff,
                'meter_size' => $meterSizes[array_rand($meterSizes)],
                'installation_date' => now()->subMonths(rand(1, 24)),
                'is_active' => $i % 7 === 0 ? false : true, // Beberapa meter nonaktif
                'last_reading' => rand(100, 500),
            ]);
        }

        // Existing test customer (ensure it uses customer_group_code if applicable)
        $customerUser = User::firstOrCreate([
            'phone' => '08444444444'
        ], [
            'role_id' => $customerRole->id,
            'name' => 'Test Customer Default',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $customer = Customer::firstOrCreate([
            'customer_number' => 'CUST001'
        ], [
            'user_id' => $customerUser->id,
            'ktp_number' => '1234567890123456',
            'address' => 'Jl. Test No. 123, Jakarta',
            'tariff_group' => 'R1',
            'power_capacity' => 900,
        ]);

        $meter = Meter::firstOrCreate([
            'meter_number' => 'MTR001'
        ], [
            'customer_id' => $customer->id,
            'meter_type' => 'digital',
            'customer_group_code' => 'R1',
            'meter_size' => '1/2"',
            'installation_date' => '2025-01-01',
            'is_active' => true,
        ]);

        // Create test billing periods (keep as is)
        $billingPeriods = [
            [
                'period_year' => 2025,
                'period_month' => 8,
                'start_date' => '2025-08-01',
                'end_date' => '2025-08-31',
                'due_date' => '2025-09-15',
                'is_active' => true,
            ],
            [
                'period_year' => 2025,
                'period_month' => 9,
                'start_date' => '2025-09-01',
                'end_date' => '2025-09-30',
                'due_date' => '2025-10-15',
                'is_active' => true,
            ]
        ];

        foreach ($billingPeriods as $period) {
            BillingPeriod::firstOrCreate([
                'period_year' => $period['period_year'],
                'period_month' => $period['period_month']
            ], $period);
        }

        $this->command->info('Test data created successfully!');
    }
}