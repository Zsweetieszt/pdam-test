<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Perubahan berdasarkan requirement baru:
     * 1. Menambah golongan pelanggan sesuai Kepbup
     * 2. Menambah ukuran water meter
     * 3. Memindahkan tariff_group dari customer ke meter (karena tarif per meter)
     * 4. Menambah meter_size untuk perhitungan biaya administrasi
     */
    public function up(): void
    {
        // Update customers table - remove tariff_group karena pindah ke meter
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('tariff_group');
        });

        // Update meters table - tambah kolom untuk requirement baru
        Schema::table('meters', function (Blueprint $table) {
            // Golongan pelanggan sesuai Kepbup
            $table->string('customer_group_code', 10)->after('meter_type');
            $table->string('customer_group_name', 100)->after('customer_group_code');
            
            // Ukuran water meter
            $table->enum('meter_size', ['1/2"', '3/4"', '1"', '1 1/2"', '2"', '3"', '4"'])->after('customer_group_name');
            
            // Tarif untuk perhitungan
            $table->decimal('block1_rate', 10, 2)->default(0)->comment('Tarif Blok I per m3');
            $table->decimal('block2_rate', 10, 2)->default(0)->comment('Tarif Blok II per m3');
            $table->decimal('block3_rate', 10, 2)->default(0)->comment('Tarif Blok III per m3');
            $table->decimal('block4_rate', 10, 2)->default(0)->comment('Tarif Blok IV per m3');
            $table->decimal('admin_fee', 10, 2)->default(0)->comment('Biaya Administrasi');
            
            // Block limits untuk perhitungan bertingkat
            $table->integer('block1_limit')->default(10)->comment('Batas Blok I (m3)');
            $table->integer('block2_limit')->default(10)->comment('Batas Blok II (m3)');
            $table->integer('block3_limit')->default(10)->comment('Batas Blok III (m3)');
        });

        // Create new table untuk master tarif sesuai Kepbup
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('Kode golongan: 1L1, 1R1, 2R1, dst');
            $table->string('name', 100)->comment('Nama golongan');
            $table->string('category', 20)->comment('Kategori: Rumah, Lembaga, Niaga, Industri, Sosial');
            $table->text('description')->nullable();
            
            // Tarif per blok
            $table->decimal('block1_rate', 10, 2)->default(0);
            $table->decimal('block2_rate', 10, 2)->default(0);
            $table->decimal('block3_rate', 10, 2)->default(0);
            $table->decimal('block4_rate', 10, 2)->default(0);
            
            // Batas blok
            $table->integer('block1_limit')->default(10);
            $table->integer('block2_limit')->default(10);
            $table->integer('block3_limit')->default(10);
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create table untuk biaya administrasi per ukuran meter
        Schema::create('meter_admin_fees', function (Blueprint $table) {
            $table->id();
            $table->enum('meter_size', ['1/2"', '3/4"', '1"', '1 1/2"', '2"', '3"', '4"']);
            $table->decimal('admin_fee', 10, 2)->comment('Biaya administrasi per bulan');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique('meter_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meter_admin_fees');
        Schema::dropIfExists('customer_groups');
        
        Schema::table('meters', function (Blueprint $table) {
            $table->dropColumn([
                'customer_group_code',
                'customer_group_name', 
                'meter_size',
                'block1_rate',
                'block2_rate', 
                'block3_rate',
                'block4_rate',
                'admin_fee',
                'block1_limit',
                'block2_limit',
                'block3_limit'
            ]);
        });
        
        Schema::table('customers', function (Blueprint $table) {
            $table->string('tariff_group', 10)->after('address');
        });
    }
};
