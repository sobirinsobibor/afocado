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
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('transaction_code', 50)->unique();
            $table->string('customer_name', 255)->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->decimal('total', 10, 2);
            $table->string('payment_method', 50)->nullable();
            $table->string('cashier_id', 100)->nullable();
            $table->string('cashier_name')->nullable();
            $table->timestamp('transaction_date')->useCurrent();
            $table->text('note');
            $table->foreignId('order_point_id')
                ->nullable()
                ->constrained('restaurant_tables');
            
            $table->string('order_point_name')->nullable();
            $table->string('order_point_type')->nullable();

            // 🔥 FLAG PENTING
            $table->boolean('is_self_order')->default(false);

            $table->timestamps();
            
            // Index untuk pencarian
            $table->index(['transaction_code']);
            $table->index(['transaction_date']);
            $table->index(['customer_name']);
            $table->index(['is_self_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_transactions');
    }
};