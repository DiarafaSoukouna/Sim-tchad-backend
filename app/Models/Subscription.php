<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'follower_id',
        'followed_id',
    ];

    public function follower()
    {
        return $this->belongsTo(Actor::class, 'follower_id');
    }

    public function followed()
    {
        return $this->belongsTo(Actor::class, 'followed_id');
    }
}
