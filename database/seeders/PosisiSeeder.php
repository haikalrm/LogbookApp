<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PosisiSeeder extends Seeder
{
    public function run()
    {
        $adminUser = User::where('email', 'admin@example.com')->first();
        
        if (!$adminUser) {
            throw new \Exception('Admin user must be seeded before positions');
        }

        $positions = [
            'Teknisi',
            'Supervisor',
            'Junior Manager',
            'Manager',
            'Deputy General Manager',
            'General Manager',
        ];

        foreach ($positions as $name) {
            DB::table('positions')->updateOrInsert(
                ['name' => $name],
                [
                    'user_id'    => $adminUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $managerPos = DB::table('positions')->where('name', 'Manager')->first();
        if ($managerPos) {
            $adminUser->update(['position_id' => $managerPos->id]); //
        }
    }
}