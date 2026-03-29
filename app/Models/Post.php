<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'actor_id',
        'description',
        'media_url',
        'media_type',
        'audience_type_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Acteur qui a créé le post
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(Actor::class, 'actor_id');
    }

    /**
     * Type d'audience ciblé
     */
    public function audienceType(): BelongsTo
    {
        return $this->belongsTo(ActorType::class, 'audience_type_id');
    }

    /**
     * Produits liés à ce post
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'post_products',
            'post_id',
            'product_id'
        );
    }
}
