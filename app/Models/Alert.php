<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $table = 'alerts';

    protected $fillable = [
        'actor_id',
        'message',
        'media_type',
        'media_url',
        'type',
    ];

    public function actor()
    {
        return $this->belongsTo(Actor::class, 'actor_id');
    }
}
