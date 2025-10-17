<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('billing_period_id')->constrained()->cascadeOnDelete();
            $table->string('bill_number', 50)->unique();
            $table->integer('previous_reading')->default(0);
            $table->integer('current_reading');
            $table->integer('usage_m3')->virtualAs('current_reading - previous_reading');
            $table->decimal('base_amount', 15, 2);
            $table->decimal('additional_charges', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->virtualAs('base_amount + additional_charges + tax_amount');
            $table->enum('status', ['pending', 'sent', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->date('issued_date');
            $table->date('due_date');
            $table->timestamps();
            
            $table->unique(['meter_id', 'billing_period_id']);
            $table->index('status');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
