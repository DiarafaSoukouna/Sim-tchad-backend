<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'sectors';
    protected $fillable = [
        'name',
        'description',
        'code',
        'is_active',
        'updated_by',
    ];
}
