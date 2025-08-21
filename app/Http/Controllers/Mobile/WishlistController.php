<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource\ProductResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    public function show()
    {
        $user = Auth::guard('user')->user();
        if ($user) {
            $products = $user->wishlistProducts()
                ->with(['tags'])
                ->orderBy('wishlists.created_at', 'desc')
                ->get();
;
            if ($products->isNotEmpty())
                return dataJson('products', ProductResource::collection($products), 'wishlist products');
            else
                return messageJson('No products found in wishlist', false, 404);
        } else {
            $guest_token = request()->header('guest_token');
            if (!$guest_token)
                return messageJson('Please add the guest token', false, 400);

            $products = Wishlist::where('guest_token', $guest_token)->with(['product.tags', 'product.wishlists'])->latest()
                ->get()->map(function ($item) {
                    return $item->product;
                });

            if ($products->isNotEmpty())
                return dataJson('products', ProductResource::collection($products), 'wishlist products');
            else
                return messageJson('no products found in wishlist', false, 404);
        }
    }

    public function addProduct(Request $request)
    {
        $product = Product::absolutelyActive()->find($request->product_id);
        if (!$product)
            return messageJson('Product not found', false, 404);

        $user = Auth::guard('user')->user();
        if ($user) {
            if ($product->wishlistUsers()->where('users.id', $user->id)->exists())
                return messageJson('This product already exists in wishlist', false, 400);

            $product->wishlistUsers()->attach($user->id, ['created_at' => now()]);
            return messageJson('Product added to wishlist');

        } else {
            $guest_token = $request->header('guest_token');
            if (!$guest_token)
                return messageJson('Please add the guest token', false, 400);

            if ($product->wishlists()->where('guest_token', $guest_token)->exists())
                return messageJson('This product already exists in wishlist', false, 400);

            $product->wishlists()->create(['guest_token' => $guest_token]);
            return messageJson('Product added to wishlist');
        }
    }

    public function removeProduct(Request $request)
    {
        $product = Product::absolutelyActive()->find($request->product_id);
        if (!$product)
            return messageJson('Product not found', false, 404);

        $user = Auth::guard('user')->user();
        if ($user) {
            if ($product->wishlistUsers()->where('id', $user->id)->exists()) {

                $product->wishlistUsers()->dettach($user->id);
                return messageJson('Product removed from wishlist');
            }
            return messageJson('This product not exists in wishlist', false, 404);

        } else {
            $guest_token = $request->header('guest_token');
            if (!$guest_token)
                return messageJson('Please add the guest token', false, 400);

            if ($product->wishlists()->where('guest_token', $guest_token)->exists()) {

                $product->wishlists()->where('guest_token', $guest_token)->delete();
                return messageJson('Product removed from wishlist');
            }
            return messageJson('This product not exists in wishlist', false, 404);
        }
    }
}
