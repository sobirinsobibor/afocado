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
        Schema::create('pos_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('pos_transaction_id')->constrained('pos_transactions')->onDelete('cascade');
            $table->foreignId('pos_product_id')->constrained('pos_products');
            $table->string('pos_product_name', 255); // snapshot saat transaksi
            $table->string('order_type');
            $table->integer('pos_quantity');
            $table->string('quantity_unit',);
            $table->decimal('price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->foreignId('tenant_id')->nullable()->constrained('pos_tenants')->nullOnDelete();
            $table->string('tenant_name');
            $table->timestamps();
            
            // Index untuk pencarian
            $table->index(['pos_transaction_id']);
            $table->index(['pos_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_transaction_items');
    }
};