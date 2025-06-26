<?php

namespace App\Http\Resources\Collection;

use App\Http\Resources\Resource\ProductOptionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

class ProductOptionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $basic = $this->collection->filter(function ($value) {
            return $value->attribute->type == 'basic';
        });
        $additional = $this->collection->filter(function ($value) {
            return $value->attribute->type == 'additional';
        });
        return [
            'basic' => $this->when($basic->isNotEmpty(), $basic->map(function ($option) use ($request) {
                $data = [
                    'id' => $option->pivot->id,
                    'name' => $option->name,
                    'price' => $option->pivot->extra_price,
                    'is_default' => boolval($option->pivot->is_default),
                    'attribute' => $option->attribute->name,
                ];
                if ($request->is('api/carts/items/*'))
                    $data['is_selected'] = $option->productAttributeOptions->filter(function ($product_option) use ($request) {
                        return $product_option->cartItems->contains('id', $request->id);
                    })->isNotEmpty();
                return $data;

            })->groupBy('attribute')->map(function ($items) {
                return $items->map(function ($item) {
                    return Arr::except($item, ['attribute']);
                });
            }), null),
            'additional' => $this->when($additional->isNotEmpty(), $additional->map(function ($option) use ($request) {
                $data = [
                    'id' => $option->pivot->id,
                    'name' => $option->name,
                    'price' => $option->pivot->extra_price,
                    'is_default' => boolval($option->pivot->is_default),
                    'attribute' => $option->attribute->name,
                ];
                if ($request->is('api/carts/items/*'))
                    $data['is_selected'] = $option->productAttributeOptions->filter(function ($product_option) use ($request) {
                        return $product_option->cartItems->contains('id', $request->id);
                    })->isNotEmpty();
                return $data;
            })->groupBy('attribute')->map(function ($items) {
                return $items->map(function ($item) {
                    return Arr::except($item, ['attribute']);
                });
            }), null),
        ];

    }
}
