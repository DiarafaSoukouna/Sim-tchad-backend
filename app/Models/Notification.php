<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'actor_id',
        'type',
        'is_read'
    ];

    public function actor()
    {
        return $this->belongsTo(Actor::class);
    }
}
