<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'description' => $this->whenHas('description'),
            'price' => $this->whenHas('price'),
            'image' => $this->whenHas('image'),
            'is_recommended' => boolval($this->is_recommended),
            'category_id' => $this->whenHas('category_id'),
            'category' => CategoryResource::collection($this->whenLoaded('category')),
            'options' => AttributeResource::collection($this->whenLoaded('options')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
