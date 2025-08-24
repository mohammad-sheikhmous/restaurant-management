<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_data', 'user_id', 'type', 'amount', 'description', 'created_at'
    ];

    protected $casts = [
        'user_data' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
