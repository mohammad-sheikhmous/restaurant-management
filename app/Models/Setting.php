<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'support_email', 'mobile', 'landline', 'address', 'latitude', 'longitude', 'facebook', 'instagram', 'whatsapp'
    ];
}
