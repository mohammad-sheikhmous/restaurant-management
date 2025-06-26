<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class OrderItem extends Model
{
    use HasTranslations;

    protected $fillable = [
        'product_id', 'product_data', 'quantity', 'base_price', 'extra_price', 'total_price',
    ];

    public $translatable = ['product_name'];

    protected $casts = [
        'product_data' => 'array'
    ];

    public function itemOptions()
    {
        return $this->hasMany(OrderItemOption::class);
    }
}
