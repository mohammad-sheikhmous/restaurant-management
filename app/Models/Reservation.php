<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'user_data',
        'res_date',
        'res_time',
        'res_duration',
        'guests_count',
        'status',
        'note'
    ];

    protected $casts = [
        'user_data' => 'array'
    ];
}
