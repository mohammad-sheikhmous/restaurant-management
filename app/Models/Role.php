<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Role extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name', 'permissions', 'status',
    ];

    public $translatable = ['name'];

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    public function getPermissionsAttribute($value)
    {
        return json_decode($value);
    }

    public function getTranslatedPermissionsAttribute()
    {
        $permissions = $this->permissions;

        return collect(config('roles.permissions.' . config('app.locale')))
            ->filter(function ($value, $key) use ($permissions) {
                return in_array($key, $permissions);
            });
    }

    public function setPermissionsAttribute($value)
    {
        $this->attributes['permissions'] = json_encode($value);
    }
}
