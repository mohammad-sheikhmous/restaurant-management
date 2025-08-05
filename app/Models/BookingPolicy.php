<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class BookingPolicy extends Model
{
    use HasTranslations;

    protected $fillable = [
        'max_revs_duration_hours',
        'max_pre_booking_days',
        'min_pre_booking_minutes',
        'revs_cancellability',
        'min_revs_cancellability_minutes',
        'revs_cancellability_ratio',
        'revs_modifiability',
        'min_revs_modifiability_minutes',
        'revs_modifiability_ratio',
        'table_combinability',
        'manual_confirmation',
        'temp_revs_conf_minutes',
        'min_people',
        'max_people',
        'interval_minutes',
        'auto_no_show_minutes',
        'deposit_system',
        'deposit_value',
        'num_of_person_per_deposit',
        'time_per_deposit',
        'deposit_customizability',
        'explanatory_notes',
    ];

    public $timestamps = false;

    public $translatable = ['explanatory_notes'];
}
