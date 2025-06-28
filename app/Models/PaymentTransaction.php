<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_kegiatan_id',
        'user_id',
        'payment_configuration_id',
        'feed_id',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'payment_details',
        'custom_data',
        'custom_files',
        'notes',
        'paid_at',
        'expires_at',
        'proof_of_payment',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'custom_data' => 'array',
        'custom_files' => 'array',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $attributes = [
        'currency' => 'IDR',
        'status' => 'pending',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentConfiguration()
    {
        return $this->belongsTo(PaymentConfiguration::class);
    }

    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'cancelled' => 'gray',
            'expired' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'paid' => 'Paid',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
            default => 'Unknown',
        };
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canBePaid()
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    public function markAsPaid($paymentMethod = null, $paymentDetails = null)
    {
        $this->update([
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'payment_details' => $paymentDetails,
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed($notes = null)
    {
        $this->update([
            'status' => 'failed',
            'notes' => $notes,
        ]);
    }

    public function markAsCancelled($notes = null)
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $notes,
        ]);
    }

    public function markAsExpired()
    {
        $this->update([
            'status' => 'expired',
        ]);
    }

    // Custom file handling methods
    public function addCustomFile($filePath, $fieldName = null)
    {
        $customFiles = $this->custom_files ?? [];

        if ($fieldName) {
            $customFiles[$fieldName] = $filePath;
        } else {
            $customFiles[] = $filePath;
        }

        $this->update(['custom_files' => $customFiles]);
    }

    public function removeCustomFile($filePath)
    {
        $customFiles = $this->custom_files ?? [];
        $customFiles = array_filter($customFiles, fn($file) => $file !== $filePath);
        $this->update(['custom_files' => array_values($customFiles)]);
    }

    public function getCustomFilesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setCustomFilesAttribute($value)
    {
        $this->attributes['custom_files'] = is_array($value) ? json_encode($value) : $value;
    }
}
