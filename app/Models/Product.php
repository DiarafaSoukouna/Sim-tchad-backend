<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
         use HasFactory;
        protected $fillable = [
        'name',
        'code',
        'description',
        'product_type_id',
        'speculation_id',
        'unit_of_measure_id',
        'production_area_id',
        'actor_id',
        'store_id',
        'quantity',
        'price',
        'origin',
        'shape',
        'currency_id',
        'measure_used',
        'photo',
        'production_date',
        'is_active',
        'updated_by',
        ];
        public function attributeValues()
{
    return $this->hasMany(AttributeValue::class);
}
public function productType()
{
    return $this->belongsTo(ProductType::class);
}
public function speculation()
{
    return $this->belongsTo(Speculation::class, 'speculation_id');
}
}
