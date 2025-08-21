<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    // The attributes that are mass assignable.
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'mobile', 'status', 'image', 'birthdate', 'email_verified_at'
    ];

    // The attributes that should be hidden for serialization.
    protected $hidden = [
        'password',
        'remember_token',
        'updated_at',
        'email_verified_at'
    ];

    // Get the attributes that should be cast.
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Accessor for full name
    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function scopeWithCurrentOrdersCount(Builder $query)
    {
        return $query->withCount([
            'orders as current_orders_count' => function ($query) {
                return $query->whereIn('status', ['pending', 'accepted', 'preparing', 'prepared', 'delivering']);
            }]);
    }

    public function scopeWithCurrentReservationsCount(Builder $query)
    {
        return $query->withCount([
            'reservations as current_reservations_count' => function ($query) {
                return $query->whereIn('status', ['pending', 'accepted']);
            }]);
    }

    public function scopeWithRejectedOrdersCount(Builder $query)
    {
        return $query->withCount([
            'orders as rejected_orders_count' => function ($query) {
                return $query->where('status', 'rejected');
            }]);
    }

    public function scopeWithCompletedOrdersCount(Builder $query)
    {
        return $query->withCount([
            'orders as completed_orders_count' => function ($query) {
                return $query->whereIn('status', ['delivered', 'picked_up']);
            }]);
    }

    public function scopeWithRejectedReservationsCount(Builder $query)
    {
        return $query->withCount([
            'reservations as rejected_reservations_count' => function ($query) {
                return $query->where('status', 'rejected');
            }]);
    }

    public function scopeWithFinishedReservationsCount(Builder $query)
    {
        return $query->withCount([
            'reservations as finished_reservations_count' => function ($query) {
                return $query->where('status', 'finished');
            }]);
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }
}
