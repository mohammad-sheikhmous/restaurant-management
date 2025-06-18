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
        if ($request->is('api/carts')) {
            $basic = $this->itemOptions->first(function ($value, $key) {
                return $value->attributeOption->attribute->type === 'basic';
            })?->attributeOption->name;

            $selected_additional_options = $this->itemOptions->filter(function ($option) {
                return $option->attributeOption->attribute->type == 'additional';
            })->map(function ($option) {
                return $option->attributeOption->name . "($option->extra_price)";
            })->implode(' + ');
        }
        $data = [
            'id' => $this->id,
            'name' => $this->product->name . ($basic ?? null ? " ($basic)" : ''),
            'description' => $this->product->description,
            'image' => $this->product->image,
            'is_simple' => $this->product->is_simple,
            'quantity' => $this->quantity,
        ];
        // for show item details
        if ($request->is('api/carts/items/*'))
            $data = [...$data, ...[
                'price' => $this->base_price,
                'attributes' => ProductOptionCollection::make($this->product->options),
            ]];
        // for show cart items
        if ($request->is('api/carts')) {
            $data = [...$data, ...[
                'base_price' => $this->base_price,
                'extra_price' => $this->extra_price,
                'total_price' => $this->total_price,
                'selected_additional_options' => $selected_additional_options != '' ? $selected_additional_options : null,
            ]];
        }
        return $data;
    }
}
