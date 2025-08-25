<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'image' => $this->whenHas('image'),
            'status' => $this->when($request->is('api/dashboard/categories'), $this->status),
            'products_count' => $this->whenCounted('products'),
            'orders_count' => $this->whenCounted('orderItems'),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'created_at' => $this->when($request->is('api/dashboard/categories'), $this->created_at?->format('Y-m-d')),
        ];
    }
}
