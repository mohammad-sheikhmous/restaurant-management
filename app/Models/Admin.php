<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'mobile', 'password', 'image', 'status', 'role_id', 'language'
    ];

    // The attributes that should be hidden for serialization.
    protected $hidden = [
        'password', 'updated_at',
    ];

    // Get the attributes that should be cast.
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'created_at' => 'datetime:Y-m-d h:m a',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasAccess($config_permission)
    {
        if (!$this->role)
            return false;

        if ($this->role->status == 0)
            return false;

        foreach ($this->role->permissions as $permission) {
            if ($permission == $config_permission)
                return true;
        }
        return false;
    }
}
