<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_kegiatan_id',
        'name',
        'description',
        'amount',
        'currency',
        'payment_methods',
        'custom_fields',
        'settings'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_methods' => 'array',
        'custom_fields' => 'array',
        'settings' => 'array',
    ];

    protected $attributes = [
        'currency' => 'IDR',
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

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function feeds()
    {
        return $this->hasMany(Feed::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereHas('feeds', function ($feedQuery) {
            $feedQuery->where(function ($q) {
                $q->whereNull('max_participants')
                    ->orWhereRaw('max_participants > (SELECT COUNT(*) FROM payment_transactions WHERE feed_id = feeds.id AND status IN ("pending", "paid"))');
            });
        });
    }

    public function scopeInactive($query)
    {
        return $query->whereHas('feeds', function ($feedQuery) {
            $feedQuery->whereNotNull('max_participants')
                ->whereRaw('max_participants <= (SELECT COUNT(*) FROM payment_transactions WHERE feed_id = feeds.id AND status IN ("pending", "paid"))');
        });
    }

    // Helper methods
    public function getIsActiveAttribute()
    {
        // Payment configuration is active if:
        // 1. It has no associated feed (always active for general use)
        // 2. It has a feed with unlimited participants (max_participants is null)
        // 3. It has a feed where current registrations < max_participants

        $feed = $this->feeds()->first();

        if (!$feed) {
            return true; // No feed associated, always active
        }

        if ($feed->max_participants === null) {
            return true; // Unlimited participants
        }

        $currentRegistrations = $feed->getTotalRegistrationsCount();
        return $currentRegistrations < $feed->max_participants;
    }

    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getTotalTransactionsAttribute()
    {
        return $this->transactions()->count();
    }

    public function getTotalRevenueAttribute()
    {
        return $this->transactions()
            ->where('status', 'paid')
            ->sum('amount');
    }

    public function getPendingTransactionsAttribute()
    {
        return $this->transactions()
            ->where('status', 'pending')
            ->count();
    }

    // Custom field validation methods
    public function getFileFields()
    {
        if (!$this->custom_fields) return [];

        return collect($this->custom_fields)
            ->filter(fn($field) => $field['type'] === 'file')
            ->mapWithKeys(fn($field) => [$field['name'] => $field])
            ->toArray();
    }

    public function validateFileUpload($fieldName, $file)
    {
        $fileFields = $this->getFileFields();

        if (!isset($fileFields[$fieldName])) {
            return ['error' => 'Invalid file field'];
        }

        $field = $fileFields[$fieldName];
        $errors = [];

        // Only allow image extensions
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower($file->getClientOriginalExtension());
        if (!in_array($fileExtension, $allowedTypes)) {
            $errors[] = "File type not allowed. Only images (jpg, jpeg, png, gif) are accepted.";
        }
        // Check file size
        if (isset($field['max_file_size'])) {
            $maxSize = $field['max_file_size'] * 1024 * 1024; // Convert MB to bytes
            if ($file->getSize() > $maxSize) {
                $errors[] = "File size exceeds maximum allowed size of {$field['max_file_size']}MB";
            }
        }
        return $errors;
    }

    public function getFileUploadRules($fieldName)
    {
        $fileFields = $this->getFileFields();

        if (!isset($fileFields[$fieldName])) {
            return ['file'];
        }

        $field = $fileFields[$fieldName];
        $rules = ['file'];

        if (isset($field['max_file_size'])) {
            $rules[] = "max:{$field['max_file_size']}";
        }

        if (isset($field['file_types'])) {
            $allowedTypes = array_map('trim', explode(',', $field['file_types']));
            $mimeTypes = $this->getMimeTypesFromExtensions($allowedTypes);
            $rules[] = "mimes:" . implode(',', $allowedTypes);
        }

        return $rules;
    }

    private function getMimeTypesFromExtensions($extensions)
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
        ];

        return collect($extensions)
            ->map(fn($ext) => $mimeTypes[$ext] ?? null)
            ->filter()
            ->toArray();
    }
}
