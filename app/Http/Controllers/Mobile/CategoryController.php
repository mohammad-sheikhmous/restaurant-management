<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategories()
    {
        $categories = Category::active()->withCount(['products' => function ($query) {
            return $query->active();
        }])->get();

        return dataJson('categories', CategoryResource::collection($categories), 'all categories');
    }

    public function allCategoriesWithProducts()
    {

    }
}
