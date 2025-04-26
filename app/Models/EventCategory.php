<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
