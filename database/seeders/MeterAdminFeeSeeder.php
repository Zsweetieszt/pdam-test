<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeterAdminFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Biaya administrasi berdasarkan ukuran water meter
     * Sesuai dengan requirement PDF dan contoh perhitungan
     */
    public function run(): void
    {
        $adminFees = [
            [
                'meter_size' => '1/2"',
                'admin_fee' => 7500, // Sesuai gambar contoh dan requirement
            ],
            [
                'meter_size' => '3/4"',
                'admin_fee' => 12000, // Berdasarkan requirement PDF
            ],
            [
                'meter_size' => '1"',
                'admin_fee' => 15000, // Berdasarkan requirement PDF
            ],
            [
                'meter_size' => '1 1/2"',
                'admin_fee' => 25000, // Berdasarkan requirement PDF
            ],
            [
                'meter_size' => '2"',
                'admin_fee' => 35000, // Berdasarkan requirement PDF
            ],
            [
                'meter_size' => '3"',
                'admin_fee' => 50000, // Berdasarkan requirement PDF
            ],
            [
                'meter_size' => '4"',
                'admin_fee' => 75000, // Berdasarkan requirement PDF
            ],
        ];

        foreach ($adminFees as $fee) {
            DB::table('meter_admin_fees')->insert(array_merge($fee, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
