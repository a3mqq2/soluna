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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['fixed', 'percentage']); // fixed amount or percentage
            $table->decimal('value', 10, 3); // discount value
            $table->decimal('minimum_amount', 10, 3)->nullable(); // minimum invoice amount to use coupon
            $table->integer('usage_limit')->nullable(); // maximum number of uses (null = unlimited)
            $table->integer('used_count')->default(0); // number of times used
            $table->date('start_date')->nullable(); // when coupon becomes active
            $table->date('end_date')->nullable(); // when coupon expires
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes for performance
            $table->index('code');
            $table->index('is_active');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};