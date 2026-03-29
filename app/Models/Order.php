<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'buyer_id',
        'seller_id',
        'status',
        'total_price',
    ];

    public function buyer()
    {
        return $this->belongsTo(Actor::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(Actor::class, 'seller_id');
    }
     public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
