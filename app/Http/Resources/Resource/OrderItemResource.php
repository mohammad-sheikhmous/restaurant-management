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
