<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderStatusHistory;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // event for creating order number
        $order->update([
            'order_number' => strtoupper(substr($order->user->first_name, -2)) . '-' .
                str_pad($order->id, 8, rand(1000000, 9999999), STR_PAD_LEFT),
        ]);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'changed_by' => auth('admin')->user()?->id
            ]);
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
