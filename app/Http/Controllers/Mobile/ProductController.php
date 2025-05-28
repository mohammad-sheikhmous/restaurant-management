<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource\CategoryResource;
use App\Http\Resources\Resource\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getProductsBySearching()
    {
        $searched_text = strip_tags(\request()->validate([
            'searched_text' => 'nullable|min:3'
        ])['searched_text']);

        $products = Product::absolutelyActive()
            ->when(\request()->tag_ids, function ($query1) {
                foreach (\request()->tag_ids as $tag_id)
                    $query1->whereHas('tags', function ($query2) use ($tag_id) {
                        $query2->where('tags.id', $tag_id);
                    });
            })
            ->where(function ($query) use ($searched_text) {
                $query->where('name->' . config('app.locale'), 'like', '%' . $searched_text . '%')
                    ->orWhere('description->' . config('app.locale'), 'like', '%' . $searched_text . '%');
            })
            ->latest()->with(['tags', 'wishlists'])->get();

        if ($products->isEmpty())
            return messageJson('no products found', false, 404);

        return dataJson('products', ProductResource::collection($products), 'searched products');
    }

    public function getProductsForHome()
    {
        $query = Product::query()->absolutelyActive();

        $most_ordered_products = $query->clone()
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(4)
            ->get();

        $latest_products = $query->clone()
            ->latest()
            ->take(4)
            ->get();

        $recommended_products = $query->clone()
            ->where('is_recommended', 1)
            ->latest()
            ->take(4)
            ->get();

        $categories = Category::active()->withCount(['products' => function ($query) {
            return $query->active();
        }])->get();

        return dataJson('data', [
            'categories' => CategoryResource::collection($categories),
            'most_ordered_products' => ProductResource::collection($most_ordered_products),
            'latest_products' => ProductResource::collection($latest_products),
            'recommended_products' => ProductResource::collection($recommended_products)
        ], 'home items');
    }

    public function show($id)
    {
        $product = Product::absolutelyActive()->with(['options.attribute', 'category'])->find($id);
        if (!$product)
            return messageJson('product not found', false, 404);

        return dataJson('product', ProductResource::make($product), 'show product details');
    }
}
