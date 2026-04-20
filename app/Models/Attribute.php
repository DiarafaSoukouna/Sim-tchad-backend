<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable =[
        'name',
        'speculation_id'

    ];
    public function speculation()
{
    return $this->belongsTo(Speculation::class, 'speculation_id');
}

}
