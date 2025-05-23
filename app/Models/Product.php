<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        return $query->whereStatus(1)
            ->whereRelation('category', 'status', 1);
//            ->tags()->where('status',1);
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
}
