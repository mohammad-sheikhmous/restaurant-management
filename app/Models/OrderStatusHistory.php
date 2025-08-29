<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $fillable = [
        'order_id', 'changed_at', 'changed_by', 'status'
    ];

    public $timestamps = false;

    protected $casts = [
        'changed_at' => 'datetime'
    ];

    public function changer()
    {
        return $this->belongsTo(Admin::class, 'changed_by');
    }
}
