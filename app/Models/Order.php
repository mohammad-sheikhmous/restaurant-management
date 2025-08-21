<?php

namespace App\Models;

use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([OrderObserver::class])]
class Order extends Model
{
    protected $fillable = [
        'order_number', 'status', 'user_id', 'user_address_id', 'user_data', 'delivery_driver_id', 'receiving_method',
        'payment_method', 'total_price', 'delivery_fee', 'discount_price', 'final_price',
        'estimated_receiving_time', 'receiving_time', 'notes'
    ];

    protected $casts = [
        'user_data' => 'array',
        'delivery_driver_data' => 'array',
        'notes' => 'array',
        'estimated_receiving_time' => 'datetime',
        'receiving_date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items');
    }

    public function deliveryDriver()
    {
        return $this->belongsTo(Admin::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    // event for creating order number
//    protected static function booted()
//    {
//        static::created(function ($order) {
//            $order->update([
//                'order_number' => strtoupper(substr(auth('user')->user()->first_name, -2)) . '-' .
//                    str_pad($order->id, 8, rand(1000000, 9999999), STR_PAD_LEFT),
//            ]);
//        });
//    }
}
