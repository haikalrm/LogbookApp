<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $table = 'positions';       // Nama tabel
    protected $primaryKey = 'no';         // Primary key
    public $incrementing = true;          // Auto increment aktif
    protected $keyType = 'int';           // PK tipe integer
    public $timestamps = false;           // Nonaktifkan timestamps (kalau tidak ada created_at/updated_at)
    
    protected $fillable = ['name'];       // Kolom yang bisa diisi
}
