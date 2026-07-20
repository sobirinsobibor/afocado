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
        Schema::create('pos_products', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->boolean('has_variants')->default(false);
            
            // Perbaiki ini - buat nullable
            $table->foreignId('tenant_id')->nullable()->constrained('pos_tenants')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->decimal('price', 10, 2);
            
            // Perbaiki juga ini
            $table->foreignId('price_unit_id')->nullable()->constrained('pos_units')->nullOnDelete();
            $table->integer('stock')->default(0);
            $table->foreignId('stock_unit_id')->nullable()->constrained('pos_units')->nullOnDelete();
            
            $table->json('photos')->nullable();
            $table->boolean('is_dine_in');
            $table->boolean('is_take_away');
            $table->timestamps();
            
            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_products');
    }
};