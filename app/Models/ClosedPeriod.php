<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ClosedPeriod extends Model
{
    use HasTranslations;

    protected $fillable = [
        'full_day', 'type_id', 'from_date', 'to_date', 'from_time', 'to_time', 'reason'
    ];

    public $translatable = ['reason'];

    public function type()
    {
        return $this->belongsTo(ReservationType::class);
    }
}
