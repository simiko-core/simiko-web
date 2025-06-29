<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnonymousEventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'feed_id',
        'name',
        'email',
        'phone',
        'custom_data',
        'custom_files',
    ];

    protected $casts = [
        'custom_data' => 'array',
        'custom_files' => 'array',
    ];

    // Relationships
    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'anonymous_registration_id');
    }

    // Helper methods
    public function getFormattedNameAttribute()
    {
        return $this->name;
    }

    public function getContactInfoAttribute()
    {
        $contact = [];
        if ($this->email) $contact[] = $this->email;
        if ($this->phone) $contact[] = $this->phone;
        return implode(' | ', $contact);
    }

    public function hasCustomData()
    {
        return !empty($this->custom_data);
    }

    public function hasCustomFiles()
    {
        return !empty($this->custom_files);
    }

    public function getCustomFileUrls()
    {
        if (!$this->custom_files) return [];

        $urls = [];
        foreach ($this->custom_files as $fieldName => $filePath) {
            $urls[$fieldName] = asset('storage/' . $filePath);
        }
        return $urls;
    }
}
