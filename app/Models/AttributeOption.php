<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AttributeOption extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name', 'category_id'
    ];

    public $timestamps = false;

    public $translatable = ['name'];

    public function attribute()
    {
        $this->belongsTo(Attribute::class);
    }
}
