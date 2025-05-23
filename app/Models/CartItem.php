<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'product_id', 'cart_id', 'base_price', 'extra_price', 'total_price', 'quantity',
    ];

    public function itemOptions()
    {
        return $this->belongsToMany(ProductAttributeOption::class, 'cart_item_options');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
