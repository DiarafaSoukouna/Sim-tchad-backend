<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $table = 'product_reviews';

    protected $fillable = [
        'product_id',
        'order_id',
        'quality_rating',
        'price_rating',
        'delivery_rating',
        'comment',
    ];

    protected $casts = [
        'quality_rating' => 'integer',
        'price_rating' => 'integer',
        'delivery_rating' => 'integer',
        'global_rating' => 'decimal:2',
    ];

    /**
     * Relation vers le produit
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relation vers la commande
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relation vers l'acteur (utilisateur)
     */
   
}