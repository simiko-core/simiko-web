<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitKegiatan extends Model
{
    public function admins()
    {
        return $this->hasOne(Admin::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function pendaftaranAnggota()
    {
        return $this->hasMany(PendaftaranAnggota::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
