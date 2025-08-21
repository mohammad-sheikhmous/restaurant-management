<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name', 'description', 'status', 'price', 'is_simple', 'image', 'category_id'
    ];

    public $translatable = ['name', 'description'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->whereStatus(1);
    }

    public function scopeInactive($query)
    {
        return $query->whereStatus(0);
    }

    public function scopeAbsolutelyActive($query)
    {
        return $query->whereStatus(1)->whereRelation('category', 'status', 1);
    }

    public function scopeEvaluation($query)
    {
        return $query->withCount(['orders as orders_count' => function (Builder $query) {
            return $query->whereBetween('orders.created_at', [now()->subDays(60), now()])
                ->select(DB::raw("count(distinct orders.id)"));
        }])
            ->withSum(['orderItems as revenue' => function ($query) {
                return $query->whereBetween('created_at', [now()->subDays(60), now()]);
            }], 'total_price')
            ->withCount(['orders as unique_users' => function (Builder $query) {
                // JSON_EXTRACT(json_column, '$.key')
                return $query->whereBetween('orders.created_at', [now()->subDays(60), now()])
                    ->select(DB::raw("count(distinct JSON_EXTRACT(user_data, '$.name'))"));
            }]);
    }

    public function scopeGlobalStats($query)
    {
        return $query->crossJoin(DB::raw("
        (select
            max(total_orders) as max_orders,
            max(unique_users) as max_unique_users,
            max(revenue) as max_revenue
            from (
                select
                oi.product_id,
                count(distinct o.id) as total_orders,
                count(distinct JSON_EXTRACT(o.user_data, '$.name')) as unique_users,
                sum(oi.total_price) as revenue
                from order_items oi join orders o on oi.order_id = o.id
                where o.created_at between subdate(now(), 60) and now()
                group by oi.product_id
            ) t1) t2
        "));
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function options()
    {
        return $this->belongsToMany(AttributeOption::class, 'product_attribute_options')
            ->withPivot('id', 'extra_price', 'is_default');
    }

    public function productAttributeOptions()
    {
        return $this->hasMany(ProductAttributeOption::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items');
    }

    public function wishlistUsers()
    {
        return $this->belongsToMany(User::class, 'wishlists');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function isFavorite()
    {
        if ($this->relationLoaded('wishlists')) {
            if (auth()->user())
                return boolval($this->wishlists->first(function ($item) {
                    return $item->user_id == auth()->user()->id;
                }));
            else
                return boolval($this->wishlists->first(function ($item) {
                    return $item->guest_token == request()->header('guest_token');
                }));
        } else if (request()->is('api/wishlists'))
            return true;
        else
            return null;
    }
}
