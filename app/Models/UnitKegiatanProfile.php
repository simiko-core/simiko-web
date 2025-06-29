<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKegiatanProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_kegiatan_id',
        'vision_mission',
        'description',
        'period',
        'background_photo',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected static function booted(): void
    {
        static::addGlobalScope('unitKegiatanProfiles', function (Builder $query) {
            $user = Filament::auth()?->user();
            if ($user && $user->hasRole('admin_ukm')) {
                $query->where('unit_kegiatan_id', $user->admin->unit_kegiatan_id);
            }
        });
    }

    public function unitKegiatan()
    {
        return $this->belongsTo(UnitKegiatan::class);
    }

    public function getVisionMissionTextAttribute()
    {
        return strip_tags($this->vision_mission);
    }
}
