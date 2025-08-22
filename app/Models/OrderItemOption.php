<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class OrderItemOption extends Model
{
    use HasTranslations;

    protected $fillable = [
        'product_attribute_option_id',
        'order_item_id',
        'option_attribute_name',
        'option_attribute_type',
        'option_name',
        'option_price'
    ];

    public $timestamps = false;

    public $translatable = [
        'option_attribute_name', 'option_name'
    ];

    public function productAttributeOption()
    {
        return $this->belongsTo(ProductAttributeOption::class);
    }
}
