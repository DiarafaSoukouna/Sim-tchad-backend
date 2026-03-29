<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActorMultipleType extends Model
{
    protected $fillable = [
        'actor_id',
        'actor_type_id',
    ];
    
      public function actor()
    {
        return $this->belongsTo(Actor::class);
    }

    public function actorType()
    {
        return $this->belongsTo(ActorType::class);
    }
}
