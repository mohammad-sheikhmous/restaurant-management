<?php

namespace App\Http\Resources\Resource;

use App\Http\Resources\Collection\ProductOptionCollection;
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
        $data = [
            'id' => $this->id,
            'name' => $this->product->name,
            'description' => $this->product->description,
            'image' => $this->product->image,
            'is_simple' => $this->product->is_simple,
            'quantity' => $this->quantity,
        ];
        // for show item details
        if ($request->is('api/carts/items/*'))
            $data = [...$data, ...[
                'price' => $this->base_price,
                'category' => CategoryResource::make($this->product->category),
                'attributes' => ProductOptionCollection::make($this->product->options),
            ]];
        // for show cart items
        if ($request->is('api/carts')) {
            $basic = $this->itemOptions->filter(function ($value, $key) {
                return $value->attributeOption->attribute->type === 'basic';
            });
            $additional = $this->itemOptions->filter(function ($value, $key) {
                return $value->attributeOption->attribute->type === 'additional';
            });
            $data = [...$data, ...[
                'base_price' => $this->base_price,
                'extra_price' => $this->extra_price,
                'total_price' => $this->total_price,
                'selected_attributes' => [
                    'basic' => $this->when($basic->isNotEmpty(), $basic->map(function ($option) {
                        return ['name' => $option->attributeOption->name];
                    }), null),
                    'additional' => $this->when($additional->isNotEmpty(), $additional->map(function ($option) {
                        return ['name' => $option->attributeOption->name];
                    }), null),
                ]
            ]];
        }
        return $data;
    }
}
