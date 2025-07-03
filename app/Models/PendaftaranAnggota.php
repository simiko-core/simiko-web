<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PendaftaranAnggota extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'unit_kegiatan_id',
        'status',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function IsOpen()
    {
        return $this->where('is_open', true)->exists();
    }
}
