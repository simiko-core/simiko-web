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

    protected $fillable = [
        'name',
        'alias',
        'category',
        'logo',
        'open_registration',
    ];

    protected $casts = [
        'logo' => 'array',
        'open_registration' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function admins()
    {
        return $this->hasOne(Admin::class);
    }

    public function pendaftaranAnggota()
    {
        return $this->hasMany(PendaftaranAnggota::class);
    }

    public function unitKegiatanProfile()
    {
        return $this->hasMany(UnitKegiatanProfile::class);
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    public function activityGalleries()
    {
        return $this->hasMany(ActivityGallery::class);
    }

    public function feeds()
    {
        return $this->hasMany(Feed::class);
    }

    public function paymentConfigurations()
    {
        return $this->hasMany(PaymentConfiguration::class);
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}
