<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionArea extends Model
{
    protected $table = 'production_areas';
     protected $fillable = [
        'name',
        'code',
        'actor_id',
        'latitude',
        'longitude',
        'address',
        'photo',
        'is_active',
        'updated_by',
    ];

}
