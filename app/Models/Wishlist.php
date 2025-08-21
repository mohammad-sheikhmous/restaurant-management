<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = ['guest_token', 'user_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
