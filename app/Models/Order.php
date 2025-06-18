<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number','status', 'user_id', 'user_address_id', 'user_data', 'delivery_driver_id', 'receiving_method',
        'payment_method', 'total_price', 'delivery_price', 'discount_price', 'final_price',
        'estimated_receiving_time', 'receiving_time', 'notes'
    ];

    protected $casts = [
        'user_data' => 'array',
        'notes' => 'array',
    ];

    public function products()
    {
        $this->belongsToMany(Product::class, 'order_items');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // event for creating order number
    protected static function booted()
    {
        static::created(function ($order) {
            $order->update([
                'order_number' => strtoupper(substr(auth('user')->user()->first_name, -2)) . '-' .
                    str_pad($order->id, 8, rand(1000000, 9999999), STR_PAD_LEFT),
            ]);
        });
    }
}
