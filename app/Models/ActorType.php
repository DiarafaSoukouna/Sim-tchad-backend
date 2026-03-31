<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActorType extends Model
{
    protected $table = 'actor_types';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'updated_by',
    ];
    public function actors()
{
    return $this->belongsToMany(Actor::class, 'actor_multiple_types', 'actor_type_id', 'actor_id');
}

  
}
