<?php

namespace App\Models;

use App\Casts\Translated;
use Illuminate\Database\Eloquent\Model;

class ReservationTable extends Model
{
    protected $table = 'reservation_table';

    protected $casts = [
        'table_data' => 'array',
    ];
}
