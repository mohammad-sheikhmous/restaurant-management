<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasTranslations;

    protected $fillable = ['question', 'answer'];

    public $translatable = ['question', 'answer'];

    protected $casts = ['created_at' => 'datetime:Y-m-d h:m a'];

    protected $hidden = ['updated_at'];
}
