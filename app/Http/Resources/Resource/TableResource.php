<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class TableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'table_num' => $this->table_num,
            'type' => $request->for == 'showing' ? $this->type->name : ['id' => $this->type->id, 'name' => $this->type->name],
            'seats_count' => $this->seats_count,
            'activation' => $this->activation,
            'occupancy_now' => $this->whenHas('active_reservation_exists',
                $this->active_reservation_exists == 1 ? 'occupied' : 'available'),
            'is_combinable' => $this->is_combinable,
            'curr_reservations_count' => $this->whenHas('curr_reservations_count'),
            'prev_reservations_count' => $this->whenHas('prev_reservations_count'),
            'next_reservation_date' => $this->whenHas('next_reservation_date'),
            'last_reservation_date' => $this->whenHas('last_reservation_date'),
            'created_at' => $this->whenHas('created_at', $this->created_at->format('Y-m-d h:m a')),
        ];
    }
}
