<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_kegiatan_id', 'type', 'title', 'content', 'image',
        'event_date', 'event_type', 'location', 'is_paid', 'price', 'payment_methods'
    ];

    protected $casts = [
        'payment_methods' => 'array',
        'event_date' => 'date',
        'is_paid' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('unitKegiatan', function (Builder $query) {
            $user = Filament::auth()?->user();
            $panel = Filament::getCurrentPanel();
            
            // Only apply scope if user is in UKM panel and has admin_ukm role
            if ($user && $user->hasRole('admin_ukm') && $panel && $panel->getId() === 'ukmPanel') {
                $query->where('unit_kegiatan_id', $user->admin->unit_kegiatan_id);
            }
        });
    }

    public function unitKegiatan()
    {
        return $this->belongsTo(UnitKegiatan::class);
    }


    public function banner()
    {
        return $this->hasOne(Banner::class);
    }

    // Scopes
    public function scopePosts($query)
    {
        return $query->where('type', 'post');
    }

    public function scopeEvents($query)
    {
        return $query->where('type', 'event');
    }

    // Check if this feed item is an event
    public function isEvent()
    {
        return $this->type === 'event';
    }

    // Check if this feed item is a post
    public function isPost()
    {
        return $this->type === 'post';
    }
}
