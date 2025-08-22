<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($request->is('*/dashboard/orders/*')) {
            $basic_opt = $this->itemOptions->first(function ($option) {
                return $option->option_attribute_type === 'basic';
            });
            if ($basic_opt) {
                if ($basic_opt->product_attribute_option_id)
                    $basic_name = $basic_opt->productAttributeOption->attributeOption->name;
                else
                    $basic_name = $basic_opt->option_name . ($this->product_id ? ' - unavailable attr' : '');
            }

            $selected_additional_options = $this->itemOptions->filter(function ($option) {
                return $option->option_attribute_type === 'additional';
            });
            $extra_price = $selected_additional_options
                ->sum(fn($option) => $option->productAttributeOption?->extra_price);

            $selected_additional_options = $selected_additional_options->map(function ($option) {
                if ($option->product_attribute_option_id)
                    return $option->productAttributeOption->attributeOption->name . "({$option->option_price})";
                else
                    return $option->option_name . "($option->option_price" . ($this->product_id ? ' - removed)' : ')');

            })->implode(' + ');

            $product_name = ($this->product?->name ?? $this->product_data['name'][config('app.locale')]) .
                ($basic_name ?? null ? " ($basic_name)" : ' ') . ($this->product_id ? '' : ' - unavailable');

            return [
                'product_id' => $this->product_id,
                'name' => $product_name,
                'selected_additional_options' => $selected_additional_options,
                'quantity' => $this->quantity,
                'base_price' => $this->base_price
            ];
        } else {

            $option_name = $this->itemOptions->first(function ($option) {
                return $option->option_attribute_type == 'basic';
            })?->option_name;

            $selected_additional_options = $this->itemOptions->filter(function ($option) {
                return $option->option_attribute_type == 'additional';
            })->map(function ($option) {
                return $option->option_name . "($option->option_price)";
            })->implode(' + ');

            return [
                'name' => $this->product_name . ($option_name ? " ($option_name)" : ''),
                'quantity' => $this->quantity,
                'base_price' => $this->base_price,
                'extra_price' => $this->extra_price,
                'total_price' => $this->total_price,
                'selected_additional_options' => $selected_additional_options != '' ? $selected_additional_options : null,
            ];
        }
    }
}
