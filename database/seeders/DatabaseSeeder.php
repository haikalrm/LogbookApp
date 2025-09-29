<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Memanggil UnitsSeeder untuk menambah data unit
        $this->call([
            UnitsSeeder::class, // Menambahkan UnitsSeeder
        ]);
    }
}
