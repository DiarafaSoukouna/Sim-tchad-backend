<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'stores';
    protected $fillable =[
        'name',
        'code',
        'description',
        'is_active',
        'actor_id',
        'latitude',
        'longitude',
        'address',
        'phone',
        'whatsapp',
        'photo',
        'updated_by'

        
    ];
}
