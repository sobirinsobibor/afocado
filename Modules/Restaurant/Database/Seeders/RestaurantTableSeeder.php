<?php

namespace Modules\Restaurant\Database\Seeders;

use Modules\Restaurant\Models\RestaurantTable;
use Illuminate\Database\Seeder;

class RestaurantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20 tables with different capacities
        for ($i = 1; $i <= 20; $i++) {
            $capacity = match (true) {
                $i <= 10 => 2, // Tables 1-10: capacity 2
                $i <= 15 => 4, // Tables 11-15: capacity 4
                $i <= 18 => 6, // Tables 16-18: capacity 6
                default => 8,  // Tables 19-20: capacity 8
            };

            RestaurantTable::create([
                'table_number' => sprintf('T%02d', $i),
                'capacity' => $capacity,
                'status' => 'available',
                'qr_code' => 'QR-TABLE-' . sprintf('%02d', $i) . '-' . uniqid(),
            ]);
        }
    }
}