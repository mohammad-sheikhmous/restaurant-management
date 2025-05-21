<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Attribute extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name', 'type',
    ];

    public $translatable = ['name'];

    public function options()
    {
        $this->hasMany(AttributeOption::class);
    }
}
