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
        Schema::create('pos_tenants', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('foodcourt_location_id')->constrained('pos_foodcourt_locations')->restrictOnDelete();
            $table->string('name', 255);
            $table->string('owner_name')->nullable();
            $table->string('owner_phone_number')->nullable();
            $table->json('photos')->nullable();
            $table->string('lokasi_tenant')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index untuk pencarian
            $table->index(['name']);
            $table->index(['owner_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_tenants');
    }
};
