<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class banner extends Model
{
    protected $fillable = ["post_id", "active"];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
