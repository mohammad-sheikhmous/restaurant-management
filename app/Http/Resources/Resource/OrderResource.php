<?php

namespace App\Http\Resources\Resource;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($request->is('api/orders'))
            $order_name = $this->items->sortByDesc('total_price')->take(3)
                ->map(function ($item) {
                    $option_name = $item->itemOptions->filter(function ($option) {
                        return $option->option_attribute_type == 'basic';
                    })->first()?->option_name;
                    return "{$this->numbersByLang($item->quantity)} {$item->product_name}" . ($option_name ? " ($option_name)" : '');
                })->implode(' + ');
        return [
            'id' => $this->id,
            'order_name' => $this->whenNotNull($order_name ?? null),
            'order_number' => $this->order_number,
            'status' => $this->status,
            'receiving_method' => $this->whenHas('receiving_method'),
            'payment_method' => $this->whenHas('payment_method'),
            'total_price' => $this->whenHas('total_price'),
            'delivery_fee' => $this->whenHas('delivery_fee'),
            'discount' => $this->whenHas('discount'),
            'final_price' => $this->final_price,
            'created_at' => $this->created_at->format('Y-m-d h:m a'),
            'estimated_receiving_time' => $this->whenHas('estimated_receiving_time', $this->estimated_receiving_time ?? 'Not yet determined'),
            'address' => $this->when($this->receiving_method == 'delivery' && $this->user_data, [
                'name' => $this->user_data['address']['name'] ?? null,
                'latitude' => $this->when(!in_array($this->status, ['cancelled', 'rejected', 'delivered']), $this->user_data['address']['latitude'] ?? null),
                'longitude' => $this->when(!in_array($this->status, ['cancelled', 'rejected', 'delivered']), $this->user_data['address']['longitude'] ?? null),
                'driver_latitude' => $this->when(!in_array($this->status, ['cancelled', 'rejected', 'delivered']), Setting::value('latitude')),
                'driver_longitude' => $this->when(!in_array($this->status, ['cancelled', 'rejected', 'delivered']), Setting::value('longitude')),
            ]),
            'items_count' => $this->items->sum('quantity'),
            'items' => $this->when(!$request->is('api/orders'), OrderItemResource::collection($this->items)),
        ];
    }

    private function numbersByLang($number): string
    {
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];


        return config('app.locale') == 'ar' ? str_replace($western, $eastern, (string)$number) : $number;
    }
}
