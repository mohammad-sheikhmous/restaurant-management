<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ReservationType extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name', 'deposit_value'
    ];

    public $timestamps = false;

    public $translatable = ['name'];
}
