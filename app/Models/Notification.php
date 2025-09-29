<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    /**
     * Mass assignment protection.
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'author_id',
        'title',
        'body',
        'profile',
        'read_at',
    ];

    /**
     * Memberi tahu Eloquent bahwa 'read_at' adalah objek tanggal (Carbon).
     * Ini memungkinkan kita menggunakan fungsi-fungsi tanggal yang praktis.
     */
    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Mendefinisikan relasi: "Sebuah Notifikasi dimiliki oleh seorang Author (User)".
     * Ini memungkinkan kita untuk memanggil $notification->author untuk mendapatkan data user.
     */
    public function author(): BelongsTo
    {
        // 'author_id' adalah foreign key di tabel notifications
        return $this->belongsTo(User::class, 'author_id');
    }
}