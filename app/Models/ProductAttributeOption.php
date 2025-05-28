<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeOption extends Model
{
    protected $fillable = [
        'product_id', 'attribute_option_id', 'extra_price',
    ];

    public $timestamps = false;

    public function cartItems()
    {
        return $this->belongsToMany(CartItem::class, 'cart_item_options');
    }

    public function attributeOption()
    {
        return $this->belongsTo(AttributeOption::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
