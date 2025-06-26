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
            $is_valid = true;
            // If the products is not deleted
            if ($this->product_id && $this->product->status == 1) {
                $basic_opt = $this->itemOptions->first(function ($option) {
                    return $option->option_data['attr_type'] === 'basic';
                });
                if ($basic_opt) {
                    if ($basic_opt->product_attribute_option_id)
                        $basic_name = $basic_opt->productAttributeOption->attributeOption->name;
                    else {
                        $basic_name = $basic_opt->option_data['name'][config('app.locale')] . ' - unavailable';
                        $is_valid = false;
                        $reason = "The custom {$basic_opt->option_data['attr_name'][config('app.locale')]} ({$basic_opt->option_data['name'][config('app.locale')]}) is no longer available.";
                    }
                }
                $selected_additional_options = $this->itemOptions->filter(function ($option) {
                    return $option->option_data['attr_type'] === 'additional';
                });
                $extra_price = $selected_additional_options
                    ->sum(fn($option) => $option->productAttributeOption?->extra_price);

                $selected_additional_options = $selected_additional_options->map(function ($option) {
                    if ($option->product_attribute_option_id)
                        return $option->productAttributeOption->attributeOption->name . "({$option->productAttributeOption->extra_price})";
                    else
                        return $option->option_data['name'][config('app.locale')] . "(removed)";

                })->implode(' + ');

            } else { // else if the product deleted
                $is_valid = false;
                $reason = "This item no longer available";
            }
        }
        $data = [
            'id' => $this->id,
            'name' => ($this->product?->name ?? $this->product_data['name'][config('app.locale')]) . ($basic_name ?? null ? " ($basic_name)" : ''),
            'description' => $this->product?->description ?? $this->product_data['description'][config('app.locale')],
            'image' => $this->product?->image ?? $this->product_data['image'],
            'is_simple' => $this->product?->is_simple,
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
                'is_valid' => $is_valid,
                'unavailability_reason' => $reason ?? null,
                'base_price' => !$is_valid ? 0 : $this->base_price,
                'extra_price' => !$is_valid ? 0 : $extra_price,
                'total_price' => !$is_valid ? 0 : ($this->base_price + $extra_price) * $this->quantity,
                'selected_additional_options' => ($selected_additional_options ?? null) != '' ? $selected_additional_options : null,
            ]];
        }
        return $data;
    }
}
