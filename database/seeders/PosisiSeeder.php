<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PosisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminUser = \App\Models\User::where('email', 'admin@example.com')->first();
        $budiUser = \App\Models\User::where('email', 'budi.teknisi@example.com')->first();
        
        if (!$adminUser || !$budiUser) {
            throw new \Exception('Users must be seeded before positions');
        }

        $positions = [
            ['name' => 'Karyawan', 'user_id' => $budiUser->id],
            ['name' => 'Junior Manager', 'user_id' => $budiUser->id],
            ['name' => 'Manager', 'user_id' => $adminUser->id],
            ['name' => 'Deputy General Manager', 'user_id' => $adminUser->id],
            ['name' => 'General Manager', 'user_id' => $adminUser->id],
        ];

        foreach ($positions as $position) {
            DB::table('positions')->insert([
                'name'       => $position['name'],
                'user_id'    => $position['user_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
