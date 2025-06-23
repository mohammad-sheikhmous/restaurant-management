<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->whenHas('id'),
            'name' => $this->whenHas('name'),
            'label' => $this->whenHas('label'),
            'city' => $this->whenHas('city'),
            'area' => $this->whenHas('area'),
            'street' => $this->whenHas('street'),
            'is_deliverable' => boolval($this->delivery_zone_id && $this->deliveryZone->status == 1),
            'longitude' => $this->whenHas('longitude'),
            'latitude' => $this->whenHas('latitude'),
            'mobile' => $this->whenHas('mobile'),
            'additional_details' => $this->whenHas('additional_details'),
        ];
    }
}
