<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = [
        'identifier',
        'code',
        'expires_at',
        'verified'
    ];

    protected $dates = ['expires_at'];
}
