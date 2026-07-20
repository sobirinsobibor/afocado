<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_product_categories', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('pos_products', function (Blueprint $table) {
            $table->foreignId('pos_product_category_id')->nullable()->after('tenant_id')->constrained('pos_product_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pos_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pos_product_category_id');
        });

        Schema::dropIfExists('pos_product_categories');
    }
};
