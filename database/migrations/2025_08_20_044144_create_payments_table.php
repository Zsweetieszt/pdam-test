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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->cascadeOnDelete();
            $table->string('payment_number', 50)->unique();
            $table->enum('payment_method', ['transfer', 'cash', 'online', 'mobile_banking']);
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('reference_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            // Add indexes
            $table->index(['status', 'payment_date']);
            $table->index('created_by');
            $table->index('verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
