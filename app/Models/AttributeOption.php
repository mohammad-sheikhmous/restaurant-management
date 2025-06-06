<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AttributeOption extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name', 'attribute_id'
    ];

    public $timestamps = false;

    public $translatable = ['name'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function productAttributeOptions()
    {
        return $this->hasMany(ProductAttributeOption::class);
    }
}
