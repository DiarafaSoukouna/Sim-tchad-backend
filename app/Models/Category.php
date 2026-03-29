<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'description',
        'code',
        'sector_id',
        'icons',
        'is_active',
        'updated_by',
    ];
}
