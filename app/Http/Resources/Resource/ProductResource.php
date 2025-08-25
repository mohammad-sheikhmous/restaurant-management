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
        if ($request->is('*/dashboard/products/*') && $request->for != 'showing')
            return [
                'id' => $this->id,
                'name' => $this->getTranslations('name'),
                'description' => $this->getTranslations('description'),
                'image' => $this->image,
                'status' => $this->status,
                'is_simple' => $this->is_simple,
                'price' => $this->is_simple ? $this->price : null,
                'is_recommended' => $this->is_recommended,
                'category_id' => $this->category_id,
                'tags' => TagResource::collection($this->tags),
                'attributes' => ProductOptionCollection::make($this->whenLoaded('options')),
            ];

        else
            return [
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->when(!$request->is('*/dashboard/products'), $this->description),
                'image' => $this->whenHas('image'),
                'price' => $this->whenHas('price'),
                'status' => $this->when($request->is('*/dashboard/*'), $this->status == 1 ? 'active' : 'inactive'),
                'is_favorite' => $this->whenNotNull($this->isFavorite()),
                'is_simple' => boolval($this->is_simple),
                'is_recommended' => boolval($this->is_recommended),
                'popularity_score' => $this->when($request->is('*/dashboard/products'), $this->popularity_score),
                'created_at' => $this->when($request->is('*/dashboard/*'), $this->created_at->format('Y-m-d')),
                'stats_factors' => $this->when($request->is('*/dashboard/products/*'), [
                    'orders_count_factor' => $this->orders_factor,
                    'revenue_factor' => $this->revenue_factor,
                    'users_count_factor' => $this->unique_users_factor,
                    'repeat_rate_factor' => $this->repeat_rate,
                    'popularity_score' => $this->popularity_score
                ]),
                'category_id' => $this->when(!$this->relationLoaded('category'), $this->category_id),
                'category' => CategoryResource::make($this->whenLoaded('category')),
                'attributes' => ProductOptionCollection::make($this->whenLoaded('options')),
                'tags' => TagResource::collection($this->whenLoaded('tags')),
            ];
    }
}
