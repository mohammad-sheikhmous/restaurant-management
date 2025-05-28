<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($request->is('api/dashboard/users'))
            $data = [
                'id' => $this->id,
                'name' => $this->first_name . ' ' . $this->last_name,
                'email' => $this->email,
                'status' => $this->status,
                'orders_count' => $this->whenHas('orders_count'),
                'reservations_count' => $this->whenHas('reservations_count'),
                'wallet_balance' => $this->wallet->balance ?? 0,
            ];
        else
            $data = [
                'id' => $this->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'mobile' => $this->mobile,
                'image' => $this->image,
                'status' => $this->status,
                'wallet_balance' => $this->when($this->relationLoaded('wallet'), $this->wallet->balance ?? 0),
                'current_orders_existence' => $this->whenHas('current_orders_count', boolval($this->current_orders_count)),
                'rejected_orders_count' => $this->whenHas('rejected_orders_count', $this->rejected_orders_count),
                'completed_orders_count' => $this->whenHas('completed_orders_count', $this->completed_orders_count),
                'current_reservations_existence' => $this->whenHas('current_reservations_count', boolval($this->current_reservation_count)),
                'rejected_reservations_count' => $this->whenHas('rejected_reservations_count', $this->rejected_reservations_count),
                'finished_reservations_count' => $this->whenHas('finished_reservations_count', $this->finished_reservations_count),
                'created_at' => $this->created_at->format('Y-m-d h:m a'),
            ];
        return $data;
    }
}
