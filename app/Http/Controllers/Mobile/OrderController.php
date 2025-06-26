<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Resource\OrderResource;
use App\Http\Resources\Resource\UserAddressResource;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\select;
use function Sodium\add;

class OrderController extends Controller
{
    public function getDetailsForCreatingOrder()
    {
        try {
            $receiving_method = \request()->receiving_method == 'delivery' ? 'delivery' : 'pick_up';
            $user = auth('user')->user()->load([
                'cart.items.product:id,status',
                'cart.items.itemOptions.productAttributeOption'
            ]);

            if (!$user->cart || $user->cart->items->isEmpty())
                return messageJson('Cart is empty, add items to cart for ordering', false, 422);

            if ($user->cart->items->first(function ($option) {
                return $option->product_id == null || $option->product->status == 0 ||
                    $option->itemOptions->first(
                        fn($itemOption) => $itemOption->option_data['attr_type'] === 'basic' &&
                            !$itemOption->product_attribute_option_id);
            }))
                return messageJson('The item no longer available.', false, 404);

            $order_details = [
                'total_price' => $user->cart->items->sum(function ($item) {
                    return
                        ($item->base_price
                            +
                            $item->itemOptions->where('option_data.attr_type', 'additional')
                                ->sum(fn($option) => $option->productAttributeOption?->extra_price))
                        *
                        $item->quantity;
                }),
                'items_count' => $user->cart->items->sum('quantity'),
                'delivery_fee' => 0,
                'discount' => 0,
            ];
            if ($receiving_method == 'delivery') {
                $user->load(['addresses' => function ($query) {
                    return $query->latest();
                }, 'addresses.deliveryZone']);
                if ($user->addresses->isEmpty())
                    return messageJson('To create your order, add your address first', false, 422);

                // to get address
                if (!\request()->address_id) {
                    $order_details['addresses'] = array_map(function ($item) {
                        return Arr::only($item, ['id', 'name', 'city', 'area', 'street', 'is_deliverable']);
                    }, UserAddressResource::collection($user->addresses)->resolve());

                    // Use usort to sort the array by 'is_deliverable' descending
                    usort($order_details['addresses'], function ($a, $b) {
                        return $b['is_deliverable'] <=> $a['is_deliverable'];
                    });
                    // Check that all user addresses are in area supported or no for delivery
                    if (!$order_details['addresses'][0]['is_deliverable']) {
                        $message = 'All your added addresses are now in areas not supported for delivery, Add new Addresses.';
                        return messageJson($message, false, 422);
                    }

                    $address = $user->addresses->first();
                } else {
                    $address = $user->addresses->first(function ($address) {
                        return $address->id == \request()->address_id;
                    });
                    if (!$address)
                        return messageJson('Address not found', false, 404);

                    // Check that selected address is in area supported or no for delivery
                    if (!$address->delivery_zone_id || $address->deliveryZone->status == 0)
                        return messageJson('Sorry, The selected address is in unsupported delivery area', false, 404);
                }
//
//                // to store distance array from calculateDistance function in cache for 10 minutes to reduce map api requests
//                if (!Cache::has("delivery_fee:u{$user->id},a{$address->id}")) {
//                    $setting = Setting::first();
//
//                    $distance = $this->calculateDistance(
//                        $setting->latitude, $setting->longitude,
//                        $address->latitude, $address->longitude
//                    );
//                    Cache::remember("delivery_fee:u{$user->id},a{$address->id}", 600, function () use ($distance) {
//                        return $distance;
//                    });
//                } else
//                    $distance = Cache::get("delivery_fee:u{$user->id},a{$address->id}");

                $order_details['delivery_fee'] = $address->distance * 100;
                $order_details['distance'] = $address->distance . 'km';
                $order_details['estimated_delivery_driver_time'] = $address->duration;
            }
            $order_details['final_price'] = $order_details['total_price'] + $order_details['delivery_fee'] - $order_details['discount'];

            return dataJson('details', $order_details, 'Order creating details');

        } catch (\Exception $exception) {
            return $exception;
        }
    }

    public function store(OrderRequest $request)
    {
        try {
            $user = auth('user')->user()->load([
                'wallet',
                'cart.items.product:id,name,description,status',
                'cart.items.itemOptions.productAttributeOption.attributeOption.attribute'
            ]);

            if (!$user->cart || $user->cart->items->isEmpty())
                return messageJson('Cart is empty, add items to cart for creating an order', false, 422);

            if ($user->cart->items->first(function ($option) {
                return $option->product_id == null || $option->product->status == 0 ||
                    $option->itemOptions->first(
                        fn($itemOption) => $itemOption->option_data['attr_type'] === 'basic' &&
                            !$itemOption->product_attribute_option_id);
            }))
                return messageJson('The item no longer available.', false, 404);

            if ($request->receiving_method == 'delivery') {
                $user->load(['addresses' => function ($query) use ($request) {
                    return $query->whereId($request->address_id)
                        ->whereNotNull('delivery_zone_id')
                        ->whereRelation('deliveryZone', 'status', 1);
                }]);
                if ($user->addresses->isEmpty())
                    return messageJson('Address id not valid', false, 422);

                $user_address = $user->addresses->first();
                $delivery_fee = $user_address->distance * 100;

//                // get distance if it is already stored in the cache
//                if (Cache::has("delivery_fee:u{$user->id},a{$user_address->id}"))
//                    $delivery_fee = Cache::get("delivery_fee:u{$user->id},a{$request->address_id}")['distance'] * 100;
//                // else calculate the distance again
//                else {
//                    $setting = Setting::first();
//                    $delivery_fee = $this->calculateDistance(
//                            $setting->latitude, $setting->longitude,
//                            $user_address->latitude, $user_address->longitude
//                        )['distance'] * 100;
//                }
            }
//            return $items;
            $total_price = $user->cart->items->sum(function ($item) {
                return
                    ($item->base_price
                        + $item->itemOptions->where('option_data.attr_type', 'additional')
                            ->whereNotNull('product_attribute_option_id')
                            ->sum(fn($option) => $option->productAttributeOption->extra_price))
                    *
                    $item->quantity;
            });
            $final_price = $total_price + ($delivery_fee ?? 0);

            if ($request->payment_method == 'wallet' && $final_price > ($user->wallet->balance ?? 0)) {
                return messageJson('Sorry, your wallet balance not enough for creating this order.');
            }

            $user_data = [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'address' => isset($user_address) ? [
                    'name' => $user_address->name,
                    'city' => $user_address->city,
                    'area' => $user_address->area,
                    'street' => $user_address->street,
                    'latitude' => $user_address->latitude,
                    'longitude' => $user_address->longitude,
                ] : null,
            ];
            DB::beginTransaction();
            $order = Order::create([
                'user_id' => $user->id,
                'user_address_id' => $request->receiving_method == 'delivery' ? $user_address->id : null,
                'user_data' => $user_data,
                'receiving_method' => $request->receiving_method,
                'payment_method' => $request->payment_method,
                'total_price' => $total_price,
                'delivery_fee' => $request->receiving_method == 'delivery' ? $delivery_fee : 0,
                'discount' => 0,
                'notes' => isset($request->order_notes) && count(array_filter($request->order_notes)) > 0 ?
                    array_filter($request->order_notes) : null,
            ]);
            // create order items and its options that associated them by cart items and its options
            $user->cart->items->each(function ($item) use ($order) {
                $order_item = $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_data' => [
                        'name' => $item->product->getTranslations('name'),
                        'description' => $item->product->getTranslations('description'),
                        'image' => $item->product->image,
                    ],
                    'quantity' => $item->quantity,
                    'base_price' => $item->base_price,
                    'extra_price' => $extra_price = $item->itemOptions->where('option_data.attr_type', 'additional')
                        ->whereNotNull('product_attribute_option_id')
                        ->sum(fn($option) => $option->productAttributeOption->extra_price),
                    'total_price' => ($item->base_price + $extra_price) * $item->quantity
                ]);
                foreach ($item->itemOptions->whereNotNull('product_attribute_option_id') as $option)
                    $order_item->itemOptions()->create([
                        'product_attribute_option_id' => $option->product_attribute_option_id,
                        'option_attribute_name' => $option->productAttributeOption->attributeOption->attribute->getTranslations('name'),
                        'option_attribute_type' => $option->productAttributeOption->attributeOption->attribute->type,
                        'option_name' => $option->productAttributeOption->attributeOption->getTranslations('name'),
                        'option_price' => $option->productAttributeOption->extra_price,
                    ]);
            });
            $user->cart->items()->delete();
            DB::commit();

            return messageJson('Your order created successfully.', true, 201);

        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
        }
    }

    public function index()
    {
        $orders = auth('user')->user()->orders()
            ->select('id', 'user_id', 'status', 'order_number', 'receiving_method', 'final_price', 'created_at')
            ->with([
                'items:id,order_id,product_data,total_price,quantity',
                'items.itemOptions:order_item_id,option_attribute_name,option_attribute_type,option_name'
            ])->get();
        if ($orders->isEmpty())
            return messageJson('Orders not found yet.', false, 404);
//return $orders;
        return dataJson('orders', OrderResource::collection($orders), 'All orders');
    }

    public function show($id)
    {
        $order = auth('user')->user()->orders()->whereId($id)->first();
        if (!$order)
            return messageJson('Order not found.', false, 404);

        return dataJson('order', OrderResource::make($order), 'Order returned successfully');
    }

    public function cancel($id)
    {
        $order = auth('user')->user()->orders()->whereId($id)->first();
        if (!$order)
            return messageJson('Order not found.', false, 404);

        if ($order->status != 'pending')
            return messageJson('You cannot cancel this order', false, 403);

        $order->update(['status' => 'cancelled']);

        return messageJson('Order cancelled successfully');
    }

    public function destroy($id)
    {
        $order = auth('user')->user()->orders()->whereId($id)->first();
        if (!$order)
            return messageJson('Order not found.', false, 404);

        if (!in_array($order->status, ['pending', 'rejected', 'cancelled', 'delivered', 'picked_up']))
            return messageJson('You cannot delete this order until received', false, 403);

        $order->delete();

        return messageJson('Order deleted successfully');
    }

    public function reorder($id)
    {
        try {
            $user = auth('user')->user();
            $order = $user->orders()->whereId($id)->first();
            if (!$order)
                return messageJson('Order not found.', false, 404);

            if (!in_array($order->status, ['cancelled', 'delivered', 'picked_up']))
                return messageJson('You cannot reorder this order now.!', false, 403);

            DB::beginTransaction();
            foreach ($order->items as $item) {
                $cart_item = $user->cart->items()->create([
                    'product_id' => $item->product_id,
                    'product_data' => $item->product_data,
                    'quantity' => $item->quantity,
                    'base_price' => $item->base_price,
                    'extra_price' => $item->extra_price,
                    'total_price' => $item->total_price,
                ]);
                foreach ($item->itemOptions as $option)
                    $cart_item->itemOptions()->create([
                        'product_attribute_option_id' => $option->product_attribute_option_id,
                        'option_data' => [
                            'attr_name' => $option->getTranslations('option_attribute_name'),
                            'attr_type' => $option->option_attribute_type,
                            'name' => $option->getTranslations('option_name'),
                            'price' => $option->option_price,
                        ]
                    ]);
            }
            DB::commit();
            return messageJson('Order items have been re-added to the cart.');

        } catch (\Exception $exception) {
            DB::rollBack();

            return $exception;
        }
    }
}
