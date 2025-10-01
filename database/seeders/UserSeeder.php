<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Jika admin belum ada, buat admin
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'admin',
                'fullname' => 'Administrator Utama',
                'gelar' => 'S.T',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'), // ganti setelah login
                'access_level' => '2',
                'profile_picture' => 'default.png', // default image
                'position' => 'System Administrator',
                'technician' => 1,
                'signature' => 'default-signature.png', // default signature
                'country' => 'Indonesia',
                'phone_number' => '08123456789',
                'address' => 'Jl. Merdeka No. 1',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'zip_code' => 10110,
                'joined' => now(),
                'remember_token' => Str::random(10),
            ]);
        }

        // Teknisi 1
        if (!User::where('email', 'budi.teknisi@example.com')->exists()) {
            User::create([
                'name' => 'Budi Santoso',
                'fullname' => 'Budi Santoso, A.Md.T',
                'gelar' => 'A.Md.T',
                'email' => 'budi.teknisi@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'access_level' => '0',
                'profile_picture' => 'default.png',
                'position' => 'Teknisi Senior',
                'technician' => 1,
                'signature' => 'default-signature.png',
                'country' => 'Indonesia',
                'phone_number' => '08123456790',
                'address' => 'Jl. Mawar No. 15',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'zip_code' => 10120,
                'joined' => now(),
                'remember_token' => Str::random(10),
            ]);
        }

        // Teknisi 2
        if (!User::where('email', 'sari.teknisi@example.com')->exists()) {
            User::create([
                'name' => 'Sari Wulandari',
                'fullname' => 'Sari Wulandari, S.T',
                'gelar' => 'S.T',
                'email' => 'sari.teknisi@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'access_level' => '0',
                'profile_picture' => 'default.png',
                'position' => 'Teknisi Junior',
                'technician' => 1,
                'signature' => 'default-signature.png',
                'country' => 'Indonesia',
                'phone_number' => '08123456791',
                'address' => 'Jl. Melati No. 22',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'zip_code' => 10130,
                'joined' => now(),
                'remember_token' => Str::random(10),
            ]);
        }

        // User biasa (bukan teknisi)
        if (!User::where('email', 'andi.supervisor@example.com')->exists()) {
            User::create([
                'name' => 'Andi Supervisor',
                'fullname' => 'Andi Supervisor, S.T, M.T',
                'gelar' => 'S.T, M.T',
                'email' => 'andi.supervisor@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'access_level' => '1',
                'profile_picture' => 'default.png',
                'position' => 'Supervisor',
                'technician' => 0,
                'signature' => 'default-signature.png',
                'country' => 'Indonesia',
                'phone_number' => '08123456792',
                'address' => 'Jl. Kenanga No. 5',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'zip_code' => 10140,
                'joined' => now(),
                'remember_token' => Str::random(10),
            ]);
        }
    }
}
