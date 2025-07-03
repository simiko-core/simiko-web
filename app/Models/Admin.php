<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'unit_kegiatan_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unitKegiatan()
    {
        return $this->belongsTo(UnitKegiatan::class);
    }
}
