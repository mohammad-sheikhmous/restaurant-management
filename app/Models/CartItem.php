<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'product_id', 'product_data', 'cart_id', 'base_price', 'extra_price', 'total_price', 'quantity',
    ];

    protected $casts = [
        'product_data' => 'array',
    ];

    public function itemOptions()
    {
        return $this->hasMany(CartItemOption::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
