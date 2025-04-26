<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unitKegiatan()
    {
        return $this->belongsTo(UnitKegiatan::class);
    }
}
