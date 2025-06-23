<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\Resource\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['products', 'orderItems' => function ($query) {
            return $query->select(DB::raw('count(distinct order_id)'));
        }])->latest()->get();

        return dataJson('categories', CategoryResource::collection($categories), 'All Categories');
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->only('name', 'status', 'parent');
        if ($request->hasFile('image'))
            $data['image'] = storeImage($request->name['en'], $request->image, 'categories');

        Category::create($data);

        return messageJson('New category created successfully.', true, 201);
    }

    public function show($id)
    {
        $category = Category::find($id)?->makeHidden(['created_at', 'parent']);
        if (!$category)
            return messageJson('Category not found.!', false, 404);

        return dataJson('category', $category, "The category with Id: {$id} returned.");
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = Category::find($id);
        if (!$category)
            return messageJson('Category not found.!', false, 404);

        $data = $request->only('name', 'status', 'parent');
        if ($request->hasFile('image'))
            $data['image'] = updateImage($request->name['en'], $category->getTranslation('name', 'en'),
                $request->image, 'categories');

        $category->update($data);

        return messageJson('The category updated successfully.');
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category)
            return messageJson('Category not found.!', false, 404);

        $category->delete();
        deleteImage($category->image, 'categories');

        return messageJson('The category deleted successfully.');
    }

    public function changeStatus($id)
    {
        $category = Category::find($id);
        if (!$category)
            return messageJson('Category not found.!', false, 404);

        $category->update(['status' => !$category->status]);

        return messageJson('The category status changed.');
    }
}
