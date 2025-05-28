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

    protected $hidden = ['updated_at'];

    protected $casts = ['created_at' => 'datetime:Y-m-d h:m a'];

    public $translatable = ['name'];

    public function options()
    {
        return $this->hasMany(AttributeOption::class);
    }

    public function productsOptions()
    {
        return $this->hasManyThrough(ProductAttributeOption::class, AttributeOption::class);
    }
}
