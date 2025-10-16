<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
        // Insert default roles
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'description' => 'Administrator dengan akses penuh',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'keuangan', 
                'description' => 'Staff keuangan untuk penagihan dan pembayaran',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'customer',
                'description' => 'Pelanggan PDAM',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'manajemen',
                'description' => 'Manajemen untuk laporan dan analisis',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
