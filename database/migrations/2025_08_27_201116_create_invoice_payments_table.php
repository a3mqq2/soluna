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
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->decimal('amount', 10, 3)->comment('Payment amount');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'other'])
                  ->default('cash')
                  ->comment('Payment method');
            $table->string('reference_number')->nullable()->comment('Payment reference/transaction number');
            $table->date('payment_date')->comment('Date of payment');
            $table->text('notes')->nullable()->comment('Payment notes');
            $table->enum('status', ['completed', 'pending', 'failed', 'cancelled'])
                  ->default('completed')
                  ->comment('Payment status');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->index(['invoice_id', 'payment_date']);
            $table->index(['payment_method', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};