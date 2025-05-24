<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource\CategoryResource;
use App\Http\Resources\Resource\ProductResource;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()->withCount(['products' => function ($query) {
            return $query->active();
        }])->get();

        return dataJson('categories', CategoryResource::collection($categories), 'all categories');
    }

    public function getCategoryProducts($id)
    {
        $category = Category::active()->find($id);
        if (!$category)
            return messageJson('category not found', false, 404);

        $products = $category->products()->active()
            ->when(\request()->tag_ids, function ($query1) {
                foreach (\request()->tag_ids as $tag_id)
                    $query1->whereHas('tags', function ($query2) use ($tag_id) {
                        $query2->where('tags.id', $tag_id);
                    });
            })->with(['tags', 'wishlists'])->get();

        return dataJson('products', ProductResource::collection($products), 'category products');
    }
}
