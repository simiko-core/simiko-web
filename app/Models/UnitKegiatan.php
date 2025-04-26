<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitKegiatan extends Model
{
    public function admins()
    {
        return $this->hasOne(Admin::class);
    }
}
