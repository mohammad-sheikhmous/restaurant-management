<?php

namespace App\Http\Resources\Resource;

use App\Http\Resources\Collection\ProductOptionCollection;
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
            'image' => $this->whenHas('image'),
            'price' => $this->whenHas('price'),
            'is_favorite' => $this->whenNotNull($this->isFavorite()),
            'is_simple' => boolval($this->is_simple),
            'is_recommended' => boolval($this->is_recommended),
            'category_id' => $this->when(!$this->relationLoaded('category'), $this->category_id),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'attributes' => ProductOptionCollection::make($this->whenLoaded('options')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
