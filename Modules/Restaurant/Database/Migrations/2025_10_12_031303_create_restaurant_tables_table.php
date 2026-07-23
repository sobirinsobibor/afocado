<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('foodcourt_location_id')->constrained('pos_foodcourt_locations')->restrictOnDelete();
            
            // 🔥 Nama tetap table_number tapi fungsinya lebih luas
            $table->string('table_number', 20)->unique(); // Bisa: TBL-001, CNT-001, PKP-001
            $table->string('name', 100); // "Meja 1", "Konter Utama", "Pickup Point A"
            
            // 🔥 Tambahan untuk mendukung fungsi Order Point
            $table->string('type', 20)->default('table'); // table, counter, pickup, delivery
            $table->integer('capacity')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            
            // QR Code
            $table->string('qr_code_path')->nullable();
            $table->string('qr_code_url')->nullable();
            
            $table->enum('status', ['available', 'occupied', 'reserved', 'inactive'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['foodcourt_location_id']);
            $table->index(['type']);
            $table->index(['status']);
            $table->index(['table_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
    }
};