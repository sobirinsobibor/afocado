<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_variant_options', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('pos_product_id')->constrained('pos_products')->onDelete('cascade');

            // self-relation: null = parent/grup varian, terisi = child/opsi
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('pos_variant_options')
                ->onDelete('cascade');

            $table->string('name', 100); // "Pilihan Nasi" (parent) / "Nasi Putih Biasa" (child)

            // hanya relevan utk parent
            $table->enum('selection_type', ['single', 'multiple'])->nullable();
            $table->boolean('is_required')->default(false); // wajib pilih grup ini atau tidak

            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            // hanya relevan utk child
            $table->decimal('price', 10, 2)->default(0); // 0 = gratis, tambah fee kalau > 0
            $table->integer('stock')->default(0);

            $table->timestamps();

            $table->index(['pos_product_id']);
            $table->index(['parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_variant_options');
    }
};