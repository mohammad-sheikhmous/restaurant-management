<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
            'name' => $this->product->name,
            'description' => $this->product->description,
            'image' => $this->product->image,
            'is_simple' => $this->product->is_simple,
            'is_recommended' => $this->product->is_recommended,
            'base_price' => $this->base_price,
            'extra_price' => $this->extra_price,
            'total_price' => $this->total_price,
            'quantity' => $this->quantity,
            'attributes' => [
                'basic' => $this->itemOptions->filter(function ($value,$key) {
                    return $value->attributeOption->attribute->type === 'basic';
                })->map(function ($option) {
                    return ['name' => $option->attributeOption->name];
                }),
                'additional' => $this->itemOptions->filter(function ($value,$key) {
                    return $value->attributeOption->attribute->type === 'additional';
                })->map(function ($option) {
                    return ['name' => $option->attributeOption->name];
                }),
            ]
        ];
    }
}
