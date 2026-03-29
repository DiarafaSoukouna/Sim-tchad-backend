<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table ='settings';
     protected $fillable = [
        'organization_acronym',
        'organization_name',
        'system_acronym',
        'system_name',
        'system_description',
        'system_slogan',
        'system_logo',
        'organization_address',
        'organization_email',
        'organization_phone',
        'organization_whatsapp',
        'organization_level_code',
        'organization_locality',
        'updated_by_actor_id',
    ];

}
