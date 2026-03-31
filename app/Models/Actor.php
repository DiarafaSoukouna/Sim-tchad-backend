<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Actor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'actors';

    protected $fillable = [
        'name',
        'code',
        'description',
        'actor',
        'actor_sigle',
        'email',
        'phone',
        'whatsapp',
        'actor_type_id',
        'is_active',
        'headquarter_photo',
        'logo',
        'address',
        'latitude',
        'longitude',
        'password',
        'updated_by_actor_id',
    ];

    protected $hidden = [
        'password',
    ];
  public function followersActors()
{
    return $this->belongsToMany(
        Actor::class,
        'subscriptions',
        'followed_id',
        'follower_id'
    );
}

public function followingActors()
{
    return $this->belongsToMany(
        Actor::class,
        'subscriptions',
        'follower_id',
        'followed_id'
    );
}
public function types()
{
    return $this->belongsToMany(ActorType::class, 'actor_multiple_types', 'actor_id', 'actor_type_id');
}
protected static function booted()
    {
        static::created(function ($model) {
            $model->code = 'ACT-' . str_pad($model->id, 4, '0', STR_PAD_LEFT);
            $model->save();
        });
    }
}
