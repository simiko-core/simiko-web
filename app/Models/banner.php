<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ["feed_id", "active"];

    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }
}
