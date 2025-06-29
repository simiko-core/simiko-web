<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Feed extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_kegiatan_id',
        'payment_configuration_id',
        'type',
        'title',
        'content',
        'image',
        'event_date',
        'event_type',
        'location',
        'is_paid',
        'max_participants',
        'registration_token'
    ];

    protected $casts = [
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

        static::creating(function ($feed) {
            if ($feed->is_paid && !$feed->registration_token) {
                $feed->registration_token = Str::random(32);
            }
        });

        static::updating(function ($feed) {
            if ($feed->is_paid && !$feed->registration_token) {
                $feed->registration_token = Str::random(32);
            }
        });
    }

    public function unitKegiatan()
    {
        return $this->belongsTo(UnitKegiatan::class);
    }

    public function paymentConfiguration()
    {
        return $this->belongsTo(PaymentConfiguration::class);
    }

    public function banner()
    {
        return $this->hasOne(Banner::class);
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'feed_id');
    }

    public function anonymousRegistrations()
    {
        return $this->hasMany(AnonymousEventRegistration::class, 'feed_id');
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

    public function scopePaidEvents($query)
    {
        return $query->where('type', 'event')->where('is_paid', true);
    }

    // Helper methods
    public function isEvent()
    {
        return $this->type === 'event';
    }

    public function isPost()
    {
        return $this->type === 'post';
    }

    public function isPaidEvent()
    {
        return $this->isEvent() && $this->is_paid;
    }

    public function getRegistrationUrl()
    {
        if (!$this->isPaidEvent() || !$this->registration_token) {
            return null;
        }

        return route('event.register', ['token' => $this->registration_token]);
    }

    public function regenerateRegistrationToken()
    {
        $this->update(['registration_token' => Str::random(32)]);
        return $this->registration_token;
    }

    public function getRegistrationsCount()
    {
        return $this->transactions()->whereIn('status', ['pending', 'paid'])->count();
    }

    public function getPaidRegistrationsCount()
    {
        return $this->transactions()->where('status', 'paid')->count();
    }

    public function getTotalAnonymousRegistrationsCount()
    {
        return $this->anonymousRegistrations()->count();
    }

    public function getTotalRegistrationsCount()
    {
        // Count both user-based and anonymous registrations
        return $this->getRegistrationsCount();
    }
}
