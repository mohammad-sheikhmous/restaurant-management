<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeOption extends Model
{
    protected $fillable = [
        'product_id', 'attribute_option_id', 'extra_price',
    ];

    public $timestamps = false;
}
