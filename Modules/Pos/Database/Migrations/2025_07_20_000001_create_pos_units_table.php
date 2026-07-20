<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        DB::table('pos_units')->insert([
            ['name' => 'Pcs', 'description' => 'Satuan umum untuk stok dan harga', 'is_active' => true],
            ['name' => 'Porsi', 'description' => 'Satuan porsi', 'is_active' => true],
            ['name' => 'Box', 'description' => 'Satuan box', 'is_active' => true],
            ['name' => 'Bungkus', 'description' => 'Satuan bungkus', 'is_active' => true],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_units');
    }
};
