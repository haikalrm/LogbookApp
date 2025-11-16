<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $table = 'positions';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $fillable = ['name', 'user_id'];

    /**
     * Get the user that owns the position.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}