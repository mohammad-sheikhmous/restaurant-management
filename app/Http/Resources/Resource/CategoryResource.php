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
        $data = parent::toArray($request);
        if (!$request->is('api/dashboard/*')) {
            $data = [
                'id' => $this->id,
                'name' => $this->name,
                'image' => $this->whenHas($this->image),
                'products_count' => $this->whenHas('products_count'),
                'products' => ProductResource::collection($this->whenLoaded('products')),
            ];
        }
        return $data;
    }
}
