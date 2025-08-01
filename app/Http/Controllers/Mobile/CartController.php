<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Collection\CartItemCollection;
use App\Http\Resources\Resource\CartItemResource;
use App\Http\Resources\Resource\ProductResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CartItemOption;
use App\Models\Product;
use App\Models\ProductAttributeOption;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $product = Product::with('productAttributeOptions.attributeOption.attribute')
                ->find($request->product_id);
            if (!$product)
                return messageJson('Product not found', false, 404);

            $user = auth('user')->user();
            if ($user)
                $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            else {
                $guest_token = $request->header('guest_token');
                if (!$guest_token)
                    return messageJson('Please, add the guest token', false, 400);

                $cart = Cart::firstOrCreate(['guest_token' => $guest_token]);
            }
            $base_price = $product->price;
            $extra_price = 0;

            $selected_basic_id = null;
            $additional_options_data = [];

            if (!$product->is_simple) {
                // check from basic option
                $basic_option = $product->productAttributeOptions->firstWhere(
                    fn($option) => $option->attributeOption->attribute->type === 'basic' &&
                        $option->id == $request->basic_option_id
                );
                if (!$basic_option)
                    return messageJson('Invalid basic option', false, 400);

                $base_price = $basic_option->extra_price;
                $selected_basic_id = $basic_option->id;
                $basic_option_data = [
                    'attr_name' => $basic_option->attributeOption->attribute->getTranslations('name'),
                    'attr_type' => $basic_option->attributeOption->attribute->type,
                    'name' => $basic_option->attributeOption->getTranslations('name'),
                    'price' => $basic_option->extra_price,
                ];
            }
            // check from additional options
            if ($request->additional_option_ids && $product->productAttributeOptions->count() > 0) {
                foreach (array_unique($request->additional_option_ids) as $id) {
                    $additional_option = $product->productAttributeOptions->firstWhere(
                        fn($option) => $option->attributeOption->attribute->type === 'additional' && $option->id == $id
                    );
                    if (!$additional_option)
                        return messageJson("Invalid additional option id: $id", false, 422);

                    $extra_price += $additional_option->extra_price; ////////////////
                    $additional_options_data[] = [
                        'product_attribute_option_id' => $additional_option->id,
                        'option_data' => [
                            'attr_name' => $additional_option->attributeOption->attribute->getTranslations('name'),
                            'attr_type' => $additional_option->attributeOption->attribute->type,
                            'name' => $additional_option->attributeOption->getTranslations('name'),
                            'price' => $additional_option->extra_price,
                        ]
                    ];
                }
            }
            // check if this item before exists
            $existing_item = $cart->items()->where('product_id', $product->id)
                ->with('itemOptions')
                ->get()
                ->first(function ($item) use ($selected_basic_id, $additional_options_data) {
                    $option_ids = $item->itemOptions->pluck('product_attribute_option_id')->toArray();
                    sort($option_ids);

                    $current_ids = array_filter([$selected_basic_id,
                        ...array_column($additional_options_data, 'product_attribute_option_id')]);
                    sort($current_ids);

                    return $current_ids == $option_ids;
                });
            // update the quantity and price if the item before exists
            if ($existing_item) {
                $existing_item->quantity += $request->quantity;
                $existing_item->base_price = $base_price;
//                $existing_item->extra_price = $extra_price;
//                $existing_item->total_price = ($base_price + $extra_price) * $existing_item->quantity;
                $existing_item->save();

                return messageJson('Product quantity updated in cart.');
            }
            DB::beginTransaction();

            // create new item if no same item before
            $cart_item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'product_data' => [
                    'name' => $product->getTranslations('name'),
                    'description' => $product->getTranslations('description'),
                    'image' => $product->image,
                ],
                'base_price' => $base_price,
//                'extra_price' => $extra_price,
//                'total_price' => ($base_price + $extra_price) * $request->quantity,
                'quantity' => $request->quantity,
            ]);
            // attach basic option to item
            if (!$product->is_simple)
                $basic_option->cartItems()->attach($cart_item->id, ['option_data' => json_encode($basic_option_data)]);

            // attach additional options to item
            $cart_item->itemOptions()->createMany($additional_options_data);

            DB::commit();
            return messageJson('Product added to cart');

        } catch (\Exception $exception) {
            DB::rollBack();

            return $exception;
        }
    }

    public function show()
    {
        $user = auth('user')->user();
        if ($user)
            $cart = Cart::with(['items' => function ($query) {
                return $query->latest();
            }, 'items.product', 'items.itemOptions.productAttributeOption.attributeOption'])
                ->firstOrCreate(['user_id' => $user->id]);
        else {
            $guest_token = \request()->header('guest_token');
            if (!$guest_token)
                return messageJson('Please add the guest token', false, 422);

            $cart = Cart::with(['items' => function ($query) {
                return $query->latest();
            }, 'items.product', 'items.itemOptions.productAttributeOption.attributeOption'])
                ->firstOrCreate(['guest_token' => $guest_token]);
        }
        if ($cart->items->isEmpty())
            return messageJson('No items found in cart', false, 404);

        return dataJson('cart', CartItemCollection::make($cart->items), 'cart items');
    }

    public function incrementItem($id)
    {
        $user = auth('user')->user();
        if ($user)
            $cart = Cart::with(['items'])->where('user_id', $user->id)->first();
        else {
            $guest_token = \request()->header('guest_token');
            if (!$guest_token)
                return messageJson('Please add the guest token', false, 400);

            $cart = Cart::with(['items.product:id,status'])->where('guest_token', $guest_token)->first();
        }
        $item = $cart->items->first(function ($item) use ($id) {
            return $item->id == $id;
        });
        if (!$item)
            return messageJson('Item not found', false, 404);

        if (!$item->product_id || $item->product->status == 0)
            return messageJson('The Item no longer available', false, 404);

        $item->quantity += 1;
        $item->total_price = ($item->base_price + $item->extra_price) * $item->quantity;
        $item->save();

        return messageJson('Item incremented');
    }

    public function decrementItem($id)
    {
        $user = auth('user')->user();
        if ($user)
            $cart = Cart::with(['items.product:id,status'])->where('user_id', $user->id)->first();
        else {
            $guest_token = \request()->header('guest_token');
            if (!$guest_token)
                return messageJson('Please add the guest token', false, 400);

            $cart = Cart::with(['items'])->where('guest_token', $guest_token)->first();
        }
        $item = $cart->items->first(function ($item) use ($id) {
            return $item->id == $id;
        });
        if (!$item)
            return messageJson('Item not found', false, 404);

        if (!$item->product_id || $item->product->status == 0)
            return messageJson('The Item no longer available', false, 404);

        if ($item->quantity == 1) {
            $item->delete();
            return messageJson('Item removed from cart');
        }
        $item->quantity -= 1;
        $item->total_price = ($item->base_price + $item->extra_price) * $item->quantity;
        $item->save();

        return messageJson('Item decremented');
    }

    public function showItem($id)
    {
        $item = CartItem::with(['product.options.attribute', 'product.options.productAttributeOptions'])
            ->find($id);
        if (!$item)
            return messageJson('Item not found', false, 404);

        if (!$item->product_id || $item->product->status == 0)
            return messageJson('The item no longer available.', false, 404);

        return dataJson('item', CartItemResource::make($item), 'Show item details');
    }

    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $user = auth('user')->user();
            if ($user)
                $cart = Cart::where('user_id', $user->id)->first();
            else {
                $guest_token = $request->header('guest_token');
                if (!$guest_token)
                    return messageJson('Please add the guest token', false, 400);

                $cart = Cart::where('guest_token', $guest_token)->first();
            }
            $item = CartItem::where('cart_id', $cart?->id)
                ->with([
                    'product.productAttributeOptions.attributeOption.attribute',
                    'itemOptions'
                ])->find($id);
            if (!$item)
                return messageJson('Item not found', false, 404);

            $base_price = $item->product->price;
            $extra_price = 0;

            $selected_basic_id = null;
            $selected_additional_ids = [];

            if (!$item->product->is_simple) {
                // check from basic option
                $basic_option = $item->product->productAttributeOptions->firstWhere(
                    fn($option) => $option->attributeOption->attribute->type === 'basic' &&
                        $option->id == $request->basic_option_id
                );
                if (!$basic_option)
                    return messageJson('Invalid basic option', false, 400);

                $base_price = $basic_option->extra_price;
                $selected_basic_id = $basic_option->id;
                $basic_option_data = [
                    'product_attribute_option_id' => $selected_basic_id,
                    'option_data' => [
                        'attr_name' => $basic_option->attributeOption->attribute->getTranslations('name'),
                        'attr_type' => $basic_option->attributeOption->attribute->type,
                        'name' => $basic_option->attributeOption->getTranslations('name'),
                        'price' => $basic_option->extra_price,
                    ]
                ];
            }
            // check from additional options
            if ($request->additional_option_ids && $item->product->productAttributeOptions->count() > 0) {
                foreach (array_unique($request->additional_option_ids) as $id) {
                    $additional_option = $item->product->productAttributeOptions->firstWhere(
                        fn($option) => $option->attributeOption->attribute->type === 'additional' && $option->id == $id
                    );
                    if (!$additional_option)
                        return messageJson("Invalid additional option id: $id", false, 400);

                    $extra_price += $additional_option->extra_price;//////////////////////
                    $selected_additional_ids[] = $additional_option->id;
                }
                sort($selected_additional_ids);
            }
            DB::beginTransaction();

            // update the item details
            $cart_item = $item->update([
                'base_price' => $base_price,
                'extra_price' => $extra_price,///////////////////////
                'total_price' => ($base_price + $extra_price) * $request->quantity,/////////////////////
                'quantity' => $request->quantity,
            ]);
            // ensure that the basic option not contained before
            if (!$item->product->is_simple && !$item->itemOptions->contains('product_attribute_option_id', $basic_option->id)) {
                // get the old basic option
                $stored_basic_option = $item->itemOptions->first(function ($option) {
                    return $option->option_data['attr_type'] == 'basic';
                });
                // detach old basic option after update
                $stored_basic_option->delete();

                // attach new basic option after update
                $item->itemOptions()->create($basic_option_data);
            }

            // get all already selected option ids
            $options = $item->itemOptions->filter(function ($option) {
                return $option->option_data['attr_type'] == 'additional';
            })->pluck('product_attribute_option_id')->toArray();

            // detach deselected options after update
            $item->itemOptions()->whereIn('product_attribute_option_id', array_diff($options, $selected_additional_ids))
                ->orWhereNull('product_attribute_option_id')
                ->delete();
            // attach new selected options after update
            $additional_options_data = [];
            foreach (array_diff($selected_additional_ids, $options) as $id) {
                $additional_option = $item->product->productAttributeOptions->firstWhere(
                    fn($option) => $option->id == $id
                );
                $additional_options_data[] = [
                    'product_attribute_option_id' => $additional_option->id,
                    'option_data' => [
                        'attr_name' => $additional_option->attributeOption->attribute->getTranslations('name'),
                        'attr_type' => $additional_option->attributeOption->attribute->type,
                        'name' => $additional_option->attributeOption->getTranslations('name'),
                        'price' => $additional_option->extra_price,
                    ]
                ];
            }
            $item->itemOptions()->createMany($additional_options_data);

            DB::commit();
            return messageJson('Item updated');

        } catch (\Exception $exception) {
            DB::rollBack();

            return $exception;
        }
    }

    public function removeItem($id)
    {
        $user = auth('user')->user();
        if ($user)
            $cart = Cart::where('user_id', $user->id)->with('items')->first();
        else {
            $guest_token = \request()->header('guest_token');
            if (!$guest_token)
                return messageJson('Please add the guest token', false, 400);

            $cart = Cart::where('guest_token', $guest_token)->with('items')->first();
        }
        $item = $cart->items->first(function ($item) use ($id) {
            return $item->id == $id;
        });
        if (!$item)
            return messageJson('Item not found', false, 404);

        $item->delete();
        return messageJson('Item removed form cart');
    }
}
