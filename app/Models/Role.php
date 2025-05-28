<?php

namespace App\Models;

use App\Casts\Translated;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Role extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name', 'permissions', 'status',
    ];

    public $translatable = ['name'];

    protected $hidden = ['updated_at'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:m a',
        'permissions' => 'array'
    ];

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    public function getTranslatedPermissionsAttribute()
    {
        $permissions = $this->permissions;

        return collect(config('roles.permissions.' . config('app.locale')))
            ->filter(function ($value, $key) use ($permissions) {
                return in_array($key, $permissions);
            });
    }
}
