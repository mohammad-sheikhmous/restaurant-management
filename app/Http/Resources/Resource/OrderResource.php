<?php

namespace App\Http\Resources\Resource;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class   OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($request->is('*/dashboard/*')) {
            $showDirection = $request->is('*/dashboard/orders/*');
            $indexDirection = $request->is('*/dashboard/orders');

            return [
                'id' => $this->id,
                'order_num' => $this->order_number,
                'user_name' => $this->when($indexDirection, $this->user ?
                    $this->user->first_name . ' ' . $this->user->last_name :
                    $this->user_data['name']
                ),
                'user_mobile' => $this->when($indexDirection,
                    $this->user ? $this->user->mobile : $this->user_data['mobile']
                ),
                'user_data' => $this->when($showDirection, [
                    'id' => $this->whenNotNull(
                        ($user = ($this->user ?? $this->user_data))['id'] ?? null
                    ),
                    'name' => $user['name'],
                    'mobile' => $user['mobile'],
                    'email' => $user['email'],
                    'delivery_address' => $this->when($this->receiving_method == 'delivery', [
                        'name' => ($address = $this->user_data['address'])['name'] ?? null,
                        'street' => $address['street'] ?? null,
                        'area' => $address['area'] ?? null,
                        'city' => $address['city'] ?? null,
                    ])
                ]),
                'receiving_method' => $this->receiving_method,
                'payment_method' => $this->payment_method,
                'total_price' => $this->when($showDirection, $this->total_price),
                'delivery_fee' => $this->when($showDirection, $this->delivery_fee),
                'discount' => $this->discount,
                'final_price' => $this->final_price,
                'delivery_driver' => $this->when($indexDirection, $this->receiving_method != 'delivery' ? 'not found' :
                    (
                    in_array($this->status, ['pending', 'cancelled', 'rejected','picked_up']) ?
                        'not specified'. ($this->status == 'pending' ? ' yet' : '') :
                        ($this->deliveryDriver->name ?? $this->delivery_driver_data->name)
                    )
                ),
                'delivery_driver_data' => $this->when($showDirection,
                    in_array($this->status, ['pending', 'cancelled', 'rejected', 'picked_up']) ? 'not specified yet' : [
                        'id' => $this->whenNotNull(
                            ($driver = ($this->deliveryDriver ?? $this->delivery_driver_data))['id'] ?? null
                        ),
                        'name' => $driver['name'],
                        'mobile' => $driver['mobile'],
                    ]
                ),
                'status' => $this->status,
                'status_logs' => $this->when($showDirection,
                    $this->statusLogs->map(function ($log) {
                        return [
                            'status' => $log->status,
                            'changed_by' => $log->changer->name ?? 'By the same user.',
                            'changed_at' => $log->changed_at->format('Y-m-d h:i a')
                        ];
                    })
                ),
                'estimated_receiving_time' => $this->estimated_receiving_time?->format('h:i a') ?? 'not specified yet',
                'created_at' => $this->created_at->format('Y-m-d h:i a'),
                'notes' => $this->when($showDirection, $this->notes),
            ];

        } else {
            if ($request->is('api/orders'))
                $order_name = $this->items->sortByDesc('total_price')->take(3)
                    ->map(function ($item) {
                        $option_name = $item->itemOptions->filter(function ($option) {
                            return $option->option_attribute_type == 'basic';
                        })->first()?->option_name;
                        return "{$this->numbersByLang($item->quantity)} {$item->product_data['name'][config('app.locale')]}" .
                            ($option_name ? " ($option_name)" : '');
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
                'created_at' => $this->created_at->format('Y-m-d h:i a'),
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
    }

    private function numbersByLang($number): string
    {
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];


        return config('app.locale') == 'ar' ? str_replace($western, $eastern, (string)$number) : $number;
    }
}
