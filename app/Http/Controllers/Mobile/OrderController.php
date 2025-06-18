<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Resource\OrderResource;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\select;

class OrderController extends Controller
{
    public function getDetailsForCreatingOrder()
    {
        try {
            $receiving_method = \request()->receiving_method == 'delivery' ? 'delivery' : 'pick_up';
            $user = auth('user')->user()->load(['cart.items']);

            if (!$user->cart || $user->cart->items->isEmpty())
                return messageJson('Cart is empty, add items to cart for ordering', false, 422);

            if ($receiving_method == 'delivery') {
                $user->load(['addresses' => function ($query) {
                    return $query->select('id', 'user_id', 'name', 'city', 'area', 'street', 'longitude', 'latitude')->latest();
                }]);
                if ($user->addresses->isEmpty())
                    return messageJson('To create your order, add your address first', false, 422);
            }
            $details = [
                'total_price' => $user->cart->items->sum('total_price'),
                'items_count' => $user->cart->items->sum('quantity'),
                'delivery_fee' => 0,
                'discount' => 0,
            ];
            if ($receiving_method == 'delivery') {
                $setting = Setting::first();

                // to get address
                if (!\request()->address_id) {
                    $details['addresses'] = $user->addresses->makeHidden(['user_id', 'longitude', 'latitude']);

                    $address = $user->addresses->first();
                } else {
                    $address = $user->addresses->first(function ($address) {
                        return $address->id == \request()->address_id;
                    });
                    if (!$address)
                        return messageJson('Address not found', false, 404);
                }
                // to store distance array from calculateDistance function in cache for 10 minutes to reduce map api requests
                if (!Cache::has("delivery_fee:u{$user->id},a{$address->id}")) {
                    $distance = $this->calculateDistance(
                        $setting->latitude, $setting->longitude,
                        $address->latitude, $address->longitude
                    );
                    Cache::remember("delivery_fee:u{$user->id},a{$address->id}", 600, function () use ($distance) {
                        return $distance;
                    });
                } else
                    $distance = Cache::get("delivery_fee:u{$user->id},a{$address->id}");

                $details['delivery_fee'] = $distance['distance'] * 100;
                $details['estimated_delivery_driver_time'] = $distance['duration'];
            }
            $details['final_price'] = $details['total_price'] + $details['delivery_fee'] - $details['discount'];

            return dataJson('details', $details, 'Order creating details');
        } catch (\Exception $exception) {
            return exceptionJson();
        }
    }

    private function calculateDistance($lat_from, $lon_from, $lat_to, $lon_to)
    {
        $origin = $lat_from . ',' . $lon_from;
        $destination = $lat_to . ',' . $lon_to;

        $response = Http::get("https://maps.googleapis.com/maps/api/directions/json", [
            'origin' => $origin,
            'destination' => $destination,
            'key' => 'AIzaSyD9zQQNoowad3i_Fycd6YrfbR2mfysHtnQ',
        ]);
        $data = $response->json();

        $distanceInMeters = $data['routes'][0]['legs'][0]['distance']['value'] ?? 0; // by meters
        $distanceInKm = round($distanceInMeters / 1000, 1);

        $durationInMinutes = $data['routes'][0]['legs'][0]['duration']['text'] ?? 0; // by minutes

        return [
            'distance' => $distanceInKm,
            'duration' => $durationInMinutes
        ];
    }

    public function store(OrderRequest $request)
    {
        try {
            $user = auth('user')->user()->load([
                'wallet',
                'cart.items.product:id,name',
                'cart.items.itemOptions.attributeOption.attribute',
            ]);

            if (!$user->cart || $user->cart->items->isEmpty())
                return messageJson('Cart is empty, add items to cart for creating an order', false, 422);

            if ($request->receiving_method == 'delivery') {
                $user->load(['addresses' => function ($query) use ($request) {
                    return $query->whereId($request->address_id);
                }]);
                if ($user->addresses->isEmpty())
                    return messageJson('Address id not valid', false, 422);

                $user_address = $user->addresses->first();
                // get distance if it is already stored in the cache
                if (Cache::has("delivery_fee:u{$user->id},a{$user_address->id}"))
                    $delivery_fee = Cache::get("delivery_fee:u{$user->id},a{$request->address_id}")['distance'] * 100;
                // else calculate the distance again
                else {
                    $setting = Setting::first();
                    $delivery_fee = $this->calculateDistance(
                            $setting->latitude, $setting->longitude,
                            $user_address->latitude, $user_address->longitude
                        )['distance'] * 100;
                }
            }
            $total_price = $user->cart->items->sum('total_price');
            $final_price = $total_price + ($delivery_fee ?? 0);

            if ($request->payment_method == 'wallet' && $final_price > ($user->wallet->balance ?? 0)) {
                return messageJson('Sorry, your wallet balance not enough for creating this order');
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
            foreach ($user->cart->items as $item) {
                $order_item = $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->getTranslations('name'),
                    'quantity' => $item->quantity,
                    'base_price' => $item->base_price,
                    'extra_price' => $item->extra_price,
                    'total_price' => $item->total_price,
                ]);
                foreach ($item->itemOptions as $option)
                    $order_item->itemOptions()->create([
                        'product_attribute_option_id' => $option->id,
                        'option_attribute_name' => $option->attributeOption->attribute->getTranslations('name'),
                        'option_attribute_type' => $option->attributeOption->attribute->type,
                        'option_name' => $option->attributeOption->getTranslations('name'),
                        'option_price' => $option->extra_price,
                    ]);
            }
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
                'items:id,order_id,product_name,total_price,quantity',
                'items.itemOptions:order_item_id,option_attribute_name,option_attribute_type,option_name'
            ])->get();
        if ($orders->isEmpty())
            return messageJson('Orders not found yet', false, 404);

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
}
