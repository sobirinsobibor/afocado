<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('pos_transaction_id')->constrained('pos_transactions')->onDelete('cascade');
            
            // Product snapshot (lengkap)
            $table->foreignId('pos_product_id')->nullable()->constrained('pos_products')->nullOnDelete();
            $table->string('pos_product_name', 255);
            $table->string('order_type');
            $table->integer('pos_quantity');
            $table->string('quantity_unit')->nullable();
            $table->decimal('base_price', 10, 2)->default(0); // Harga asli produk
            $table->decimal('final_price', 10, 2); // Harga setelah varian
            $table->decimal('subtotal', 10, 2);
            
            // Varian snapshot (JSON)
            $table->json('variant_details')->nullable(); // Array varian yang dipilih
            $table->string('variant_info')->nullable(); // String gabungan nama varian
            $table->decimal('variant_surcharge', 10, 2)->default(0); // Total tambahan varian
            
            // Tenant snapshot
            $table->foreignId('tenant_id')->nullable()->constrained('pos_tenants')->nullOnDelete();
            $table->string('tenant_name');
            $table->string('foodcourt_location')->nullable();
            
            // Notes
            $table->text('note_item')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Index
            $table->index(['pos_transaction_id']);
            $table->index(['pos_product_id']);
            $table->index(['order_type']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_transaction_items');
    }
};