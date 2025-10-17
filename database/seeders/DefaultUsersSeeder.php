<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get role IDs
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $keuanganRole = DB::table('roles')->where('name', 'keuangan')->first();
        $manajemenRole = DB::table('roles')->where('name', 'manajemen')->first();

        // Insert default users
        DB::table('users')->insert([
            [
                'role_id' => $adminRole->id,
                'name' => 'Administrator',
                'phone' => '08111111111',
                'password' => Hash::make('Password123'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_id' => $keuanganRole->id,
                'name' => 'Staff Keuangan',
                'phone' => '08222222222',
                'password' => Hash::make('Password123'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'role_id' => $manajemenRole->id,
                'name' => 'Staff Manajemen',
                'phone' => '08333333333',
                'password' => Hash::make('Password123'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
