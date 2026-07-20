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
        Schema::create('pos_variant_options', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('pos_product_id')->constrained('pos_products')->onDelete('cascade'); // denormalisasi untuk query lebih cepat
            $table->string('name', 100); // "S", "M", "L"
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->timestamps();
            
            // Index untuk pencarian
            $table->index(['pos_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_variant_options');
    }
};