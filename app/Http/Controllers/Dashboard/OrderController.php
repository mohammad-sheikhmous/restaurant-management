<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource\OrderResource;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()
            ->with(['user:id,first_name,last_name,mobile', 'deliveryDriver:id,name,mobile'])
            ->paginate(\request()->limit ?? 10);;

        $page = numberToOrdinalWord(request()->page ?? 1);

        return dataJson(
            'orders',
            (OrderResource::collection($orders))->response()->getData(true),
            "All Orders for {$page} page."
        );
    }

    public function show($id)
    {
        $order = Order::with([
            'user:id,first_name,last_name,mobile,email',
            'deliveryDriver:id,name,mobile',
            'statusLogs.changer:id,name',
            'items.itemOptions.productAttributeOption.attributeOption',
            'items.product'
        ])
            ->where('id', $id)
            ->first();
        if (!$order)
            return messageJson('Order not found.!', false, 404);

        return dataJson(
            'orders',
            OrderResource::make($order),
            "Orders with $id returned."
        );
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order)
            return messageJson('Order not found.!', false, 404);

        if (!in_array($order->status, ['cancelled', 'rejected', 'delivered', 'picked_up']))
            return messageJson("You can't delete this order because is active.!", false, 403);

        $order->delete();

        return messageJson('Order deleted successfully.');
    }

    public function changeStatus($id)
    {
        request()->validate([
            'status' => [
                'required',
                'string',
                'in:cancelled,accepted,rejected,preparing,prepared,delivering,delivered,picked_up'
            ],
            'estimated_delivery_time' => ['nullable', 'required_if:status,accepted', 'bail', 'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $time = Carbon::createFromFormat('H:i', $value);
                    if ($time->lessThanOrEqualTo(now()))
                        $fail('The :attribute must be after current time.');
                }
            ],
        ]);
        $order = Order::find($id);
        if (!$order)
            return messageJson('Order not found.!', false, 404);

        $new_status = request()->status;
        $current_status = $order->status;

        // Logical sequence of statuses
        $validTransitions = [
            'pending' => ['accepted', 'cancelled', 'rejected'],
            'accepted' => ['preparing', 'cancelled'],
            'preparing' => ['prepared', 'cancelled'],
            'prepared' => ['delivering', 'picked_up'],
            'delivering' => ['delivered'],
            'delivered' => [],
            'cancelled' => [],
            'rejected' => [],
            'picked_up' => []
        ];

        if (!in_array($new_status, $validTransitions[$current_status] ?? [])) {
            return messageJson("We can't move from $current_status to $new_status.!", false, 422);
        }

        $order->update(['status' => $new_status]);

        return messageJson("The order is $new_status.");
    }
}
