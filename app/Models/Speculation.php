<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Speculation extends Model
{
    protected $table = 'speculations';
    protected $fillable = [
        'name',
        'description',
        'code',
        'photo',
        'category_id',
        'is_active',
        'updated_by',
    ];
    public function categorie()
{
    return $this->belongsTo(Category::class, 'category_id');
}
}
