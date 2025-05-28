<?php

namespace App\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
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
            'type' => $this->type,
            'created_at' => $this->created_at->format('Y-m-d h:m a'),
            'options' => $this->whenLoaded('options',$this->options->map(function ($option) {
                return [
                    'id' => $option->id,
                    'name' => $option->name,
                    'attribute_id' => $option->attribute_id
                ];
            })),
        ];
    }
}
