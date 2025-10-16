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

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test customer user
        $customerRole = Role::where('name', 'customer')->first();
        
        $customerUser = User::firstOrCreate([
            'phone' => '08444444444'
        ], [
            'role_id' => $customerRole->id,
            'name' => 'Test Customer',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        // Create a test customer
        $customer = Customer::firstOrCreate([
            'customer_number' => 'CUST001'
        ], [
            'user_id' => $customerUser->id,
            'ktp_number' => '1234567890123456',
            'address' => 'Jl. Test No. 123, Jakarta',
            'tariff_group' => 'R1',
        ]);

        // Create a test meter
        $meter = Meter::firstOrCreate([
            'meter_number' => 'MTR001'
        ], [
            'customer_id' => $customer->id,
            'meter_type' => 'digital',
            'installation_date' => '2025-01-01',
            'is_active' => true,
        ]);

        // Create test billing periods
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
        $this->command->info("Customer ID: {$customer->id}");
        $this->command->info("Meter ID: {$meter->id}");
        $this->command->info("Billing Period IDs: " . BillingPeriod::pluck('id')->implode(', '));
    }
}
