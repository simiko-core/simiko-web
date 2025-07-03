<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'feed_id',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }
}
