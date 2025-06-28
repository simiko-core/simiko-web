<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;


class ActivityGallery extends Model
{

    protected static function booted(): void
    {
        static::addGlobalScope("unitKegiatan", function (Builder $query) {
            $user = Filament::auth()?->user();
            if ($user && $user->hasRole("admin_ukm")) {
                $query->where(
                    "unit_kegiatan_id",
                    $user->admin->unit_kegiatan_id
                );
            }
        });
    }


    protected $fillable = [
        'unit_kegiatan_id',
        'image',
        'caption'
    ];

    public function unitKegiatan()
    {
        return $this->belongsTo(UnitKegiatan::class);
    }
}
