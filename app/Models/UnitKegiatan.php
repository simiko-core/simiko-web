<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKegiatan extends Model
{
    use HasFactory;

    // protected static function booted(): void
    // {
    //     static::addGlobalScope('unitKegiatan', function (Builder $query) {
    //         $user = Filament::auth()?->user();
    //         if ($user && $user->hasRole('admin_ukm')) {
    //             $query->where('unit_kegiatan_id', $user->admin->unit_kegiatan_id);
    //         }
    //     });
    // }

    protected $casts = [
        'logo' => 'array',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

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

    public function unitKegiatanProfile()
    {
        return $this->hasMany(UnitKegiatanProfile::class);
    }
}
