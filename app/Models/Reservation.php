<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'user_data',
        'revs_date',
        'revs_time',
        'revs_duration',
        'guests_count',
        'status',
        'note',
        'deposit_value',
        'deposit_status',
    ];

    protected $casts = [
        'user_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tables()
    {
        return $this->belongsToMany(Table::class, 'reservation_table');
    }

    public function reservationTables()
    {
        return $this->hasMany(ReservationTable::class);
    }
}
