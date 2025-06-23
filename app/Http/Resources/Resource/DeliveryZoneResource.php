<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryZoneResource extends JsonResource
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
            'name' => $request->is('api/dashboard/zones/*') ? $this->getTranslations('name') : $this->name,
            'coordinates' => json_decode($this->coordinates),
            'status' => $this->when($request->is('api/dashboard/*'), $this->status),
            'associated_users_count' => $this->whenCounted('users')
        ];
    }
}
