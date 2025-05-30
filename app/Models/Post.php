<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $hidden = [
        'created_at',
        'updated_at',
        'unit_kegiatan_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('unitKegiatan', function (Builder $query) {
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
}
