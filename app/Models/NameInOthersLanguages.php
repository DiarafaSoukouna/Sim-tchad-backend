<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Language;


class NameInOthersLanguages extends Model
{
    protected $table = 'name_in_others_languages';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'language_id',
        'name',
    ];
    public function language()
{
    return $this->belongsTo(Language::class, 'language_id');
}
}
