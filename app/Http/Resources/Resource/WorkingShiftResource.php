<?php

namespace App\Http\Resources\Resource;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkingShiftResource extends JsonResource
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
            'day_of_week' => $this->day_of_week,
            'type' => $request->is('*/shifts') ? $this->type->name : $this->type,
            'opening_time' => Carbon::parse($this->opening_time)->format('H:i'),
            'closing_time' => Carbon::parse($this->closing_time)->format('H:i'),
        ];
    }
}
