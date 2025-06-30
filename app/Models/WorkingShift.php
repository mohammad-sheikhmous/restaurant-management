<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WorkingShift extends Model
{
    protected $fillable = [
        'day_of_week', 'type_id', 'opening_time', 'closing_time'
    ];

    public $timestamps = false;

    public function type()
    {
        return $this->belongsTo(ReservationType::class);
    }
}
