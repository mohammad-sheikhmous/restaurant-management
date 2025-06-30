<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'table_num', 'seats_count', 'activation', 'is_combinable', 'type_id',
    ];

    public function type()
    {
        return $this->belongsTo(ReservationType::class, 'type_id');
    }

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_table');
    }

    public function setActivationAttribute($val)
    {
        $this->attributes['activation'] = $val == 'active' ? 1 : 0;
    }

    public function getActivationAttribute($val)
    {
        return $val == 1 ? 'active' : 'inactive';
    }

    public function nextReservation()
    {
        return $this->hasOne(Reservation::class)
            ->whereStatus('accepted')
            ->orderByRaw('res_date, res_time');
    }

    public function lastReservation()
    {
        return $this->hasOne(Reservation::class)
            ->whereStatus('accepted')
            ->orderByRaw('res_date desc, res_time desc');
    }
}
