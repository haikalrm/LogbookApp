<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import class HasMany

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    /**
     * Menonaktifkan timestamps `created_at` dan `updated_at`.
     * PASTIKAN tabel 'users' Anda tidak memiliki kolom ini.
     * Jika ada, hapus baris ini.
     */
    public $timestamps = false;

    protected $fillable = [
        'name', 'gelar', 'username', 'email', 'password',
        'access_level', 'profile_picture', 'position',
        'technician', 'signature', 'country',
        'phone_number', 'address', 'city', 'state', 'zip_code', 'joined'
    ];

    /**
     * Memberi tahu Laravel untuk selalu memperlakukan kolom ini sebagai objek tanggal.
     * INI MEMPERBAIKI ERROR "format() on null".
     */
    protected $casts = [
        'joined' => 'datetime',
    ];

    // METHOD 'username()' SUDAH DIHAPUS UNTUK MEMPERBAIKI ERROR RELATIONSHIP

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'author_id');
    }
	
	public function recentDevices() {
        return $this->hasMany(RecentDevice::class)->latest('last_login');
    }
}