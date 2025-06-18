<?php

namespace App\Http\Resources\Collection;

use App\Http\Resources\Resource\CartItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CartItemCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'items_count' => $this->collection->sum('quantity'),
            'cart_total_price' => $this->collection->sum('total_price'),
            'items' => CartItemResource::collection($this->collection),
        ];
    }
}
