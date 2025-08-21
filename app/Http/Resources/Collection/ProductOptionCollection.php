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
        if ($request->is('*/dashboard/products/*')) {
            if ($request->for == 'showing') {
                return $this->collection->map(function ($option) {
                    return [
                        'id' => $option->pivot->id,
                        'name' => $option->name,
                        'attribute' => $option->attribute->name,
                        'type' => $option->attribute->type,
                        'price' => $option->pivot->extra_price,
                        'is_default' => boolval($option->pivot->is_default),
                    ];
                })->toArray();
            } else {
                $basic_options = $this->collection->filter(function ($option) {
                    return $option->attribute->type == 'basic';
                });
                return [
                    'basic' => $this->when($basic_options->isNotEmpty(), [
                        'id' => $basic_options->first()?->attribute->id,
                        'attribute' => $basic_options->first()?->attribute->name,
                        'options' => $basic_options->map(function ($option) {
                            return [
                                'id' => $option->id,
                                'name' => $option->name,
                                'price' => $option->pivot->extra_price,
                                'is_default' => boolval($option->pivot->is_default),
                            ];
                        })
                    ], null),
                    'additional' => $this->when($basic_options->isNotEmpty(),
                        $this->collection->filter(function ($option) {
                            return $option->attribute->type == 'additional';
                        })->groupBy('attribute_id')->map(function ($additional_options) {
                            return [
                                'id' => $additional_options->first()->attribute_id,
                                'attribute' => $additional_options->first()->attribute->name,
                                'options' => $additional_options->map(function ($option) {
                                    return [
                                        'id' => $option->id,
                                        'name' => $option->name,
                                        'price' => $option->pivot->extra_price,
                                        'is_default' => boolval($option->pivot->is_default),
                                    ];
                                })
                            ];
                        })->toArray(), null)
                ];
            }
        } else {
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
}
