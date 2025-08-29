<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CartItemOption extends Model
{
    use HasTranslations;

    protected $fillable = [
        'product_attribute_option_id', 'option_data','cart_item_id'
    ];

    public $timestamps = false;


    protected $casts = [
        'option_data' => 'array'
    ];

    public function productAttributeOption()
    {
        return $this->belongsTo(ProductAttributeOption::class);
    }
}
