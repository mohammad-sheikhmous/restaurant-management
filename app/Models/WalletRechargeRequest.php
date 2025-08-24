<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletRechargeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'user_data', 'amount', 'transfer_method', 'status', 'proof_image', 'note','created_at'
    ];

    protected $casts = [
        'user_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
