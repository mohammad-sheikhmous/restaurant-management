<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'revs_number',
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
        'created_at'
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

    protected static function booted()
    {
        static::creating(function ($reservation) {
            $reservation->revs_number = self::generateReservationNumber($reservation);
        });
    }

    private static function generateReservationNumber($reservation): string
    {
        $date = $reservation->created_at?->format('Ymd') ?? now()->format('Ymd');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return "R-{$date}-{$random}";
    }
}
