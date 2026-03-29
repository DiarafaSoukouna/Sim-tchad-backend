<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
        protected $fillable = [
        'attribute_id',
        'product_id',
        'value',
    ];
}
