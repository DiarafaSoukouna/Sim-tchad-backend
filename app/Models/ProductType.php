<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductType extends Model
{
      use HasFactory;
    protected $table = 'product_types';
      protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'updated_by'
    ];
    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }

}
