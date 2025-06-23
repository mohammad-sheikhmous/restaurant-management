<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class DeliveryZone extends Model
{
    use HasTranslations;

    protected $table = 'delivery_zones';

    public $timestamps = false;

    protected $fillable = [
        'name', 'coordinates', 'status'
    ];

    public $translatable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_addresses')->distinct();
    }
}
