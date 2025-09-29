<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogbookTeknisi extends Model
{
    use HasFactory;

    protected $fillable = ['logbook_id', 'user_id'];

    public function logbook()
    {
        return $this->belongsTo(Logbook::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
