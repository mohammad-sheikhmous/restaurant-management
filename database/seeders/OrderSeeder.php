<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /************  Order 1 ************/

        $user1 = User::with('addresses')->find(1);
        $driver1 = Admin::find(2);
        $product1 = Product::with('options.attribute')->find(2);
        $option_default1 = $product1->options
            ->where('attribute.type', 'basic')
            ->where('pivot.is_default', true)
            ->first();
        $options1 = $product1->options->where('attribute.type', 'additional')->take(2);

        $orders = [
            [
                'status' => 'delivered',
                'user_id' => 1,
                'user_data' => $user_data1 = [
                    'name' => $user1->name,
                    'email' => $user1->email,
                    'mobile' => $user1->mobile,
                    'address' => $user1->addresses->isNotEmpty() ? [
                        'name' => $user1->addresses->first()->name,
                        'city' => $user1->addresses->first()->city,
                        'area' => $user1->addresses->first()->area,
                        'street' => $user1->addresses->first()->street,
                        'latitude' => $user1->addresses->first()->latitude,
                        'longitude' => $user1->addresses->first()->longitude,
                    ] : null,
                ],
                'delivery_driver_id' => $driver1->id,
                'delivery_driver_data' => [
                    'name' => $driver1->name,
                    'mobile' => $driver1->mobile,
                ],
                'receiving_method' => 'delivery',
                'payment_method' => 'cash',
                'delivery_fee' => 1000,
                'discount' => 4000,
                'estimated_receiving_time' => '16:00',
                'notes' => null,
                'created_at' => now()->subDays(30)->setTime(14, 30)
            ]
        ];
        $items = [
            [
                'order_id' => 1,
                'product_id' => $product1->id,
                'product_data' => [
                    'name' => $product1->getTranslations('name'),
                    'description' => $product1->getTranslations('description'),
                    'image' => $product1->image,
                ],
                'quantity' => 3,
                'base_price' => $product1->price,
                'extra_price' => $extra_price = $options1->sum('pivot->extra_price'),
                'total_price' => $total_price = ($product1->price + $extra_price) * 3
            ],
        ];
        $orders[0]['total_price'] = $total_price;

        $options = [];
        foreach ($options1 as $key => $option) {
            $options[] = [
                'order_item_id' => 1,
                'product_attribute_option_id' => $option->pivot->id,
                'option_attribute_name' => $option->attribute->getTranslations('name'),
                'option_attribute_type' => $option->attribute->type,
                'option_name' => $option->getTranslations('name'),
                'option_price' => $option->pivot->extra_price,
            ];
        }
        $options[] = [
            'order_item_id' => 1,
            'product_attribute_option_id' => $option_default1->pivot->id,
            'option_attribute_name' => $option_default1->attribute->getTranslations('name'),
            'option_attribute_type' => $option_default1->attribute->type,
            'option_name' => $option_default1->getTranslations('name'),
            'option_price' => $option_default1->pivot->extra_price,
        ];
        $status_logs1 = [
            [
                'order_id' => 1,
                'status' => 'accepted',
                'changed_at' => now()->subDays(30)->setTime(14, 40),
                'changed_by' => 1
            ],
            [
                'order_id' => 1,
                'status' => 'preparing',
                'changed_at' => now()->subDays(30)->setTime(14, 45),
                'changed_by' => 1
            ],
            [
                'order_id' => 1,
                'status' => 'prepared',
                'changed_at' => now()->subDays(30)->setTime(15, 20),
                'changed_by' => 1
            ],
            [
                'order_id' => 1,
                'status' => 'delivering',
                'changed_at' => now()->subDays(30)->setTime(15, 30),
                'changed_by' => 2
            ],
            [
                'order_id' => 1,
                'status' => 'delivered',
                'changed_at' => now()->subDays(30)->setTime(15, 55),
                'changed_by' => 2
            ],
        ];

        /**********  Order 2  ***********/

        $user2 = User::with('addresses')->find(2);
        $driver2 = Admin::find(3);
        $product2 = Product::with('options.attribute')->find(3);
        $option_default2 = $product2->options
            ->where('attribute.type', 'basic')
            ->where('pivot.is_default', true)
            ->first();
        $options2 = $product2->options->where('attribute.type', 'additional')->take(3);

        $orders[] = [
            'status' => 'delivered',
            'user_id' => 2,
            'user_data' => [
                'name' => $user2->name,
                'email' => $user2->email,
                'mobile' => $user2->mobile,
                'address' => $user2->addresses->isNotEmpty() ? [
                    'name' => $user2->addresses->first()->name,
                    'city' => $user2->addresses->first()->city,
                    'area' => $user2->addresses->first()->area,
                    'street' => $user2->addresses->first()->street,
                    'latitude' => $user2->addresses->first()->latitude,
                    'longitude' => $user2->addresses->first()->longitude,
                ] : null,
            ],
            'delivery_driver_id' => $driver2->id,
            'delivery_driver_data' => [
                'name' => $driver2->name,
                'mobile' => $driver2->mobile,
            ],
            'receiving_method' => 'delivery',
            'payment_method' => 'cash',
            'delivery_fee' => 7000,
            'discount' => 5000,
            'estimated_receiving_time' => '20:00',
            'notes' => null,
            'created_at' => now()->subDays(28)->setTime(18, 14)
        ];
        $items[] = [
            'order_id' => 2,
            'product_id' => $product2->id,
            'product_data' => [
                'name' => $product2->getTranslations('name'),
                'description' => $product2->getTranslations('description'),
                'image' => $product2->image,
            ],
            'quantity' => 5,
            'base_price' => $product2->price,
            'extra_price' => $extra_price = $options2->sum('pivot->extra_price'),
            'total_price' => $total_price = ($product2->price + $extra_price) * 5

        ];
        $orders[1]['total_price'] = $total_price;

        foreach ($options2 as $option) {
            $options[] = [
                'order_item_id' => 2,
                'product_attribute_option_id' => $option->pivot->id,
                'option_attribute_name' => $option->attribute->getTranslations('name'),
                'option_attribute_type' => $option->attribute->type,
                'option_name' => $option->getTranslations('name'),
                'option_price' => $option->pivot->extra_price,
            ];
        }
        $options[] = [
            'order_item_id' => 2,
            'product_attribute_option_id' => $option_default2->pivot->id,
            'option_attribute_name' => $option_default2->attribute->getTranslations('name'),
            'option_attribute_type' => $option_default2->attribute->type,
            'option_name' => $option_default2->getTranslations('name'),
            'option_price' => $option_default2->pivot->extra_price,
        ];
        $status_logs2 = [
            [
                'order_id' => 2,
                'status' => 'accepted',
                'changed_at' => now()->subDays(28)->setTime(18, 25),
                'changed_by' => 1
            ],
            [
                'order_id' => 2,
                'status' => 'preparing',
                'changed_at' => now()->subDays(30)->setTime(18, 40),
                'changed_by' => 1
            ],
            [
                'order_id' => 2,
                'status' => 'prepared',
                'changed_at' => now()->subDays(30)->setTime(19, 15),
                'changed_by' => 1
            ],
            [
                'order_id' => 2,
                'status' => 'delivering',
                'changed_at' => now()->subDays(30)->setTime(19, 20),
                'changed_by' => 3
            ],
            [
                'order_id' => 2,
                'status' => 'delivered',
                'changed_at' => now()->subDays(30)->setTime(19, 45),
                'changed_by' => 3
            ],
        ];

        /******** Order 3 *********/

        $driver3 = Admin::find(4);
        $product3 = Product::with('options.attribute')->find(4);
        $product4 = Product::with('options.attribute')->find(5);
        $options3 = $product3->options->where('attribute.type', 'additional')->take(2);
        $options4 = $product4->options->where('attribute.type', 'additional')->take(1);
        $option_default3 = $product3->options
            ->where('attribute.type', 'basic')
            ->where('pivot.is_default', true)
            ->first();
        $option_default4 = $product4->options
            ->where('attribute.type', 'basic')
            ->where('pivot.is_default', true)
            ->first();

        $orders[] = [
            'status' => 'delivered',
            'user_id' => $user2->id,
            'user_data' => [
                'name' => $user2->name,
                'email' => $user2->email,
                'mobile' => $user2->mobile,
                'address' => $user2->addresses->isNotEmpty() ? [
                    'name' => $user2->addresses->first()->name,
                    'city' => $user2->addresses->first()->city,
                    'area' => $user2->addresses->first()->area,
                    'street' => $user2->addresses->first()->street,
                    'latitude' => $user2->addresses->first()->latitude,
                    'longitude' => $user2->addresses->first()->longitude,
                ] : null,
            ],
            'delivery_driver_id' => $driver3->id,
            'delivery_driver_data' => [
                'name' => $driver3->name,
                'mobile' => $driver3->mobile,
            ],
            'receiving_method' => 'delivery',
            'payment_method' => 'cash',
            'delivery_fee' => 5000,
            'discount' => 6000,
            'estimated_receiving_time' => '15:00',
            'notes' => null,
            'created_at' => now()->subDays(25)->setTime(14, 0)
        ];
        $items[] = [
            'order_id' => 3,
            'product_id' => $product3->id,
            'product_data' => [
                'name' => $product3->getTranslations('name'),
                'description' => $product3->getTranslations('description'),
                'image' => $product3->image,
            ],
            'quantity' => 2,
            'base_price' => $product3->price,
            'extra_price' => $extra_price = $options3->sum('pivot->extra_price'),
            'total_price' => $total_price = ($product3->price + $extra_price) * 2
        ];
        $items[] = [
            'order_id' => 3,
            'product_id' => $product4->id,
            'product_data' => [
                'name' => $product4->getTranslations('name'),
                'description' => $product4->getTranslations('description'),
                'image' => $product4->image,
            ],
            'quantity' => 1,
            'base_price' => $product4->price,
            'extra_price' => $extra_price = $options4->sum('pivot->extra_price'),
            'total_price' => $total_price = ($product4->price + $extra_price) * 1
        ];
        $orders[2]['total_price'] = $total_price;

        foreach ($options3 as $option) {
            $options[] = [
                'order_item_id' => 3,
                'product_attribute_option_id' => $option->pivot->id,
                'option_attribute_name' => $option->attribute->getTranslations('name'),
                'option_attribute_type' => $option->attribute->type,
                'option_name' => $option->getTranslations('name'),
                'option_price' => $option->pivot->extra_price,
            ];
        }
        foreach ($options4 as $option) {
            $options[] = [
                'order_item_id' => 4,
                'product_attribute_option_id' => $option->pivot->id,
                'option_attribute_name' => $option->attribute->getTranslations('name'),
                'option_attribute_type' => $option->attribute->type,
                'option_name' => $option->getTranslations('name'),
                'option_price' => $option->pivot->extra_price,
            ];
        }
        $options[] = [
            'order_item_id' => 3,
            'product_attribute_option_id' => $option_default3->pivot->id,
            'option_attribute_name' => $option_default3->attribute->getTranslations('name'),
            'option_attribute_type' => $option_default3->attribute->type,
            'option_name' => $option_default3->getTranslations('name'),
            'option_price' => $option_default3->pivot->extra_price,
        ];
        $options[] = [
            'order_item_id' => 4,
            'product_attribute_option_id' => $option_default4->pivot->id,
            'option_attribute_name' => $option_default4->attribute->getTranslations('name'),
            'option_attribute_type' => $option_default4->attribute->type,
            'option_name' => $option_default4->getTranslations('name'),
            'option_price' => $option_default4->pivot->extra_price,
        ];
        $status_logs3 = [
            [
                'order_id' => 3,
                'status' => 'accepted',
                'changed_at' => now()->subDays(25)->setTime(14, 5),
                'changed_by' => 1
            ],
            [
                'order_id' => 3,
                'status' => 'preparing',
                'changed_at' => now()->subDays(25)->setTime(14, 10),
                'changed_by' => 1
            ],
            [
                'order_id' => 3,
                'status' => 'prepared',
                'changed_at' => now()->subDays(25)->setTime(14, 35),
                'changed_by' => 1
            ],
            [
                'order_id' => 3,
                'status' => 'delivering',
                'changed_at' => now()->subDays(25)->setTime(14, 40),
                'changed_by' => 4
            ],
            [
                'order_id' => 3,
                'status' => 'delivered',
                'changed_at' => now()->subDays(25)->setTime(15, 15),
                'changed_by' => 4
            ],
        ];

        /********  Order 4  ********/

        $user3 = User::find(3);
        $product5 = Product::with('options.attribute')->find(5);
        $product6 = Product::with('options.attribute')->find(6);
        $options5 = $product5->options->where('attribute.type', 'additional')->take(3);
        $options6 = $product6->options->where('attribute.type', 'additional')->take(2);
        $option_default5 = $product5->options
            ->where('attribute.type', 'basic')
            ->where('pivot.is_default', true)
            ->first();
        $option_default6 = $product6->options
            ->where('attribute.type', 'basic')
            ->where('pivot.is_default', true)
            ->first();

        $orders[] = [
            'status' => 'picked_up',
            'user_id' => $user3->id,
            'user_data' => [
                'name' => $user3->name,
                'email' => $user3->email,
                'mobile' => $user3->mobile,
                'address' => $user3->addresses->isNotEmpty() ? [
                    'name' => $user3->addresses->first()->name,
                    'city' => $user3->addresses->first()->city,
                    'area' => $user3->addresses->first()->area,
                    'street' => $user3->addresses->first()->street,
                    'latitude' => $user3->addresses->first()->latitude,
                    'longitude' => $user3->addresses->first()->longitude,
                ] : null,
            ],
            'receiving_method' => 'pick_up',
            'payment_method' => 'cash',
            'discount' => 2000,
            'estimated_receiving_time' => '12:00',
            'notes' => ['سأتناول الوجبة في المطعم', 'زيادة ملح', 'زيادة كتشب'],
            'created_at' => now()->subDays(23)->setTime(10, 50)
        ];
        $items[] = [
            'order_id' => 4,
            'product_id' => $product5->id,
            'product_data' => [
                'name' => $product5->getTranslations('name'),
                'description' => $product5->getTranslations('description'),
                'image' => $product5->image,
            ],
            'quantity' => 1,
            'base_price' => $product5->price,
            'extra_price' => $extra_price = $options5->sum('pivot->extra_price'),
            'total_price' => $total_price = ($product5->price + $extra_price) * 1
        ];
        $items[] = [
            'order_id' => 4,
            'product_id' => $product6->id,
            'product_data' => [
                'name' => $product6->getTranslations('name'),
                'description' => $product6->getTranslations('description'),
                'image' => $product6->image,
            ],
            'quantity' => 2,
            'base_price' => $product6->price,
            'extra_price' => $extra_price = $options6->sum('pivot->extra_price'),
            'total_price' => $total_price = ($product6->price + $extra_price) * 2
        ];
        $orders[3]['total_price'] = $total_price;

        foreach ($options5 as $option) {
            $options[] = [
                'order_item_id' => 5,
                'product_attribute_option_id' => $option->pivot->id,
                'option_attribute_name' => $option->attribute->getTranslations('name'),
                'option_attribute_type' => $option->attribute->type,
                'option_name' => $option->getTranslations('name'),
                'option_price' => $option->pivot->extra_price,
            ];
        }
        foreach ($options6 as $option) {
            $options[] = [
                'order_item_id' => 6,
                'product_attribute_option_id' => $option->pivot->id,
                'option_attribute_name' => $option->attribute->getTranslations('name'),
                'option_attribute_type' => $option->attribute->type,
                'option_name' => $option->getTranslations('name'),
                'option_price' => $option->pivot->extra_price,
            ];
        }
        $options[] = [
            'order_item_id' => 5,
            'product_attribute_option_id' => $option_default5->pivot->id,
            'option_attribute_name' => $option_default5->attribute->getTranslations('name'),
            'option_attribute_type' => $option_default5->attribute->type,
            'option_name' => $option_default5->getTranslations('name'),
            'option_price' => $option_default5->pivot->extra_price,
        ];
        $options[] = [
            'order_item_id' => 6,
            'product_attribute_option_id' => $option_default6->pivot->id,
            'option_attribute_name' => $option_default6->attribute->getTranslations('name'),
            'option_attribute_type' => $option_default6->attribute->type,
            'option_name' => $option_default6->getTranslations('name'),
            'option_price' => $option_default6->pivot->extra_price,
        ];
        $status_logs4 = [
            [
                'order_id' => 4,
                'status' => 'accepted',
                'changed_at' => now()->subDays(23)->setTime(11, 3),
                'changed_by' => 1
            ],
            [
                'order_id' => 4,
                'status' => 'preparing',
                'changed_at' => now()->subDays(23)->setTime(11, 15),
                'changed_by' => 1
            ],
            [
                'order_id' => 4,
                'status' => 'prepared',
                'changed_at' => now()->subDays(23)->setTime(11, 40),
                'changed_by' => 1
            ],
            [
                'order_id' => 4,
                'status' => 'picked_up',
                'changed_at' => now()->subDays(23)->setTime(12, 12),
            ],
        ];

        /***********  Order 5  ************/

        $user4 = User::find(4);
        $options7 = $product5->options->where('attribute.type', 'additional')->take(1);
        $options8 = $product6->options->where('attribute.type', 'additional')->take(1);

        $orders[] = [
            'status' => 'cancelled',
            'user_id' => $user4->id,
            'user_data' => [
                'name' => $user4->name,
                'email' => $user4->email,
                'mobile' => $user4->mobile,
                'address' => $user4->addresses->isNotEmpty() ? [
                    'name' => $user4->addresses->first()->name,
                    'city' => $user4->addresses->first()->city,
                    'area' => $user4->addresses->first()->area,
                    'street' => $user4->addresses->first()->street,
                    'latitude' => $user4->addresses->first()->latitude,
                    'longitude' => $user4->addresses->first()->longitude,
                ] : null,
            ],
            'receiving_method' => 'pick_up',
            'payment_method' => 'cash',
            'discount' => 0,
            'estimated_receiving_time' => '01:45',
            'notes' => ['زيادة ملح'],
            'created_at' => now()->subDays(20)->setTime(12, 43)
        ];
        $items[] = [
            'order_id' => 5,
            'product_id' => $product5->id,
            'product_data' => [
                'name' => $product5->getTranslations('name'),
                'description' => $product5->getTranslations('description'),
                'image' => $product5->image,
            ],
            'quantity' => 3,
            'base_price' => $product5->price,
            'extra_price' => $extra_price = $options5->sum('pivot->extra_price'),
            'total_price' => $total_price = ($product5->price + $extra_price) * 3
        ];
        $items[] = [
            'order_id' => 5,
            'product_id' => $product6->id,
            'product_data' => [
                'name' => $product6->getTranslations('name'),
                'description' => $product6->getTranslations('description'),
                'image' => $product6->image,
            ],
            'quantity' => 2,
            'base_price' => $product6->price,
            'extra_price' => $extra_price = $options6->sum('pivot->extra_price'),
            'total_price' => $total_price = ($product6->price + $extra_price) * 2
        ];
        $orders[4]['total_price'] = $total_price;

        foreach ($options7 as $option) {
            $options[] = [
                'order_item_id' => 7,
                'product_attribute_option_id' => $option->pivot->id,
                'option_attribute_name' => $option->attribute->getTranslations('name'),
                'option_attribute_type' => $option->attribute->type,
                'option_name' => $option->getTranslations('name'),
                'option_price' => $option->pivot->extra_price,
            ];
        }
        foreach ($options8 as $option) {
            $options[] = [
                'order_item_id' => 8,
                'product_attribute_option_id' => $option->pivot->id,
                'option_attribute_name' => $option->attribute->getTranslations('name'),
                'option_attribute_type' => $option->attribute->type,
                'option_name' => $option->getTranslations('name'),
                'option_price' => $option->pivot->extra_price,
            ];
        }
        $options[] = [
            'order_item_id' => 7,
            'product_attribute_option_id' => $option_default5->pivot->id,
            'option_attribute_name' => $option_default5->attribute->getTranslations('name'),
            'option_attribute_type' => $option_default5->attribute->type,
            'option_name' => $option_default5->getTranslations('name'),
            'option_price' => $option_default5->pivot->extra_price,
        ];
        $options[] = [
            'order_item_id' => 8,
            'product_attribute_option_id' => $option_default6->pivot->id,
            'option_attribute_name' => $option_default6->attribute->getTranslations('name'),
            'option_attribute_type' => $option_default6->attribute->type,
            'option_name' => $option_default6->getTranslations('name'),
            'option_price' => $option_default6->pivot->extra_price,
        ];
        $status_logs5 = [
            [
                'order_id' => 5,
                'status' => 'cancelled',
                'changed_at' => now()->subDays(20)->setTime(12, 55),
            ],
        ];

        /**********  Order 6  **********/

        $product7 = Product::with('options.attribute')->find(7);

        $orders[] = [
            'status' => 'rejected',
            'user_id' => $user1->id,
            'user_data' => $user_data1,
            'receiving_method' => 'delivery',
            'payment_method' => 'cash',
            'discount' => 0,
            'delivery_fee' => 10000,
            'created_at' => now()->subDays(18)->setTime(12, 39)
        ];
        $items[] = [
            'order_id' => 6,
            'product_id' => $product7->id,
            'product_data' => [
                'name' => $product7->getTranslations('name'),
                'description' => $product7->getTranslations('description'),
                'image' => $product7->image,
            ],
            'quantity' => 10,
            'base_price' => $product7->price,
            'extra_price' => 0,
            'total_price' => $total_price = ($product7->price + 0) * 10
        ];
        $orders[5]['total_price'] = $total_price;

        $status_logs6 = [
            [
                'order_id' => 6,
                'status' => 'rejected',
                'changed_at' => now()->subDays(19)->setTime(1, 3),
                'changed_by' => 1
            ],
        ];

        /**********  Order 7  ***********/

        $user5 = User::with('addresses')->find(5);
        $product8 = Product::with('options.attribute')->find(16);
        $option_default8 = $product8->options
            ->where('attribute.type', 'basic')
            ->where('pivot.is_default', true)
            ->first();

        $options8 = $product8->options->where('attribute.type', 'additional')->take(3);
        $product9 = Product::with('options.attribute')->find(16);
        $option_default9 = $product9->options
            ->where('attribute.type', 'basic')
            ->where('pivot.is_default', true)
            ->first();
        $options9 = $product9->options->where('attribute.type', 'additional')->take(3);

        $orders[] = [
            'status' => 'cancelled',
            'user_id' => 5,
            'user_data' => [
                'name' => $user5->name,
                'email' => $user5->email,
                'mobile' => $user5->mobile,
                'address' => $user5->addresses->isNotEmpty() ? [
                    'name' => $user5->addresses->first()->name,
                    'city' => $user5->addresses->first()->city,
                    'area' => $user5->addresses->first()->area,
                    'street' => $user5->addresses->first()->street,
                    'latitude' => $user5->addresses->first()->latitude,
                    'longitude' => $user5->addresses->first()->longitude,
                ] : null,
                'delivery_driver_id' => $driver1->id,
                'delivery_driver_data' => [
                    'name' => $driver1->name,
                    'mobile' => $driver1->mobile,
                ],
            ],
            'receiving_method' => 'delivery',
            'payment_method' => 'cash',
            'delivery_fee' => 7500,
            'discount' => 6000,
            'estimated_receiving_time' => '14:15',
            'notes' => ['الاتصال عند الوصول', 'زيادة كريم توم'],
            'created_at' => now()->subDays(15)->setTime(13, 8)
        ];
        $items[] = [
            'order_id' => 7,
            'product_id' => $product8->id,
            'product_data' => [
                'name' => $product8->getTranslations('name'),
                'description' => $product8->getTranslations('description'),
                'image' => $product8->image,
            ],
            'quantity' => 3,
            'base_price' => $product8->price,
            'extra_price' => $extra_price = $options8->sum('pivot->extra_price'),
            'total_price' => $total_price = ($product8->price + $extra_price) * 3

        ];
        $orders[6]['total_price'] = $total_price;

        foreach ($options8 as $option) {
            $options[] = [
                'order_item_id' => 9,
                'product_attribute_option_id' => $option->pivot->id,
                'option_attribute_name' => $option->attribute->getTranslations('name'),
                'option_attribute_type' => $option->attribute->type,
                'option_name' => $option->getTranslations('name'),
                'option_price' => $option->pivot->extra_price,
            ];
        }
        foreach ($options9 as $option) {
            $options[] = [
                'order_item_id' => 10,
                'product_attribute_option_id' => $option->pivot->id,
                'option_attribute_name' => $option->attribute->getTranslations('name'),
                'option_attribute_type' => $option->attribute->type,
                'option_name' => $option->getTranslations('name'),
                'option_price' => $option->pivot->extra_price,
            ];
        }
        $options[] = [
            'order_item_id' => 9,
            'product_attribute_option_id' => $option_default8->pivot->id,
            'option_attribute_name' => $option_default8->attribute->getTranslations('name'),
            'option_attribute_type' => $option_default8->attribute->type,
            'option_name' => $option_default8->getTranslations('name'),
            'option_price' => $option_default8->pivot->extra_price,
        ];
        $options[] = [
            'order_item_id' => 10,
            'product_attribute_option_id' => $option_default9->pivot->id,
            'option_attribute_name' => $option_default9->attribute->getTranslations('name'),
            'option_attribute_type' => $option_default9->attribute->type,
            'option_name' => $option_default9->getTranslations('name'),
            'option_price' => $option_default9->pivot->extra_price,
        ];
        $status_logs7 = [
            [
                'order_id' => 7,
                'status' => 'accepted',
                'changed_at' => now()->subDays(15)->setTime(13, 14),
                'changed_by' => 1
            ],
            [
                'order_id' => 7,
                'status' => 'preparing',
                'changed_at' => now()->subDays(15)->setTime(13, 19),
                'changed_by' => 1
            ],
            [
                'order_id' => 7,
                'status' => 'cancelled',
                'changed_at' => now()->subDays(15)->setTime(13, 32),
                'changed_by' => 1
            ],
        ];

        /**********  Order 8  **********/

        $orders[] = [
            'status' => 'picked_up',
            'user_id' => $user1->id,
            'user_data' => $user_data1,
            'receiving_method' => 'pick_up',
            'payment_method' => 'wallet',
            'discount' => 0,
            'delivery_fee' => 10000,
            'estimated_receiving_time' => '03:00',
            'created_at' => now()->subDays(12)->setTime(12, 39)
        ];
        $items[] = [
            'order_id' => 8,
            'product_id' => $product7->id,
            'product_data' => [
                'name' => $product7->getTranslations('name'),
                'description' => $product7->getTranslations('description'),
                'image' => $product7->image,
            ],
            'quantity' => 1,
            'base_price' => $product7->price,
            'extra_price' => 0,
            'total_price' => $total_price = ($product7->price + 0) * 1
        ];
        $orders[7]['total_price'] = $total_price;

        $status_logs8 = [
            [
                'order_id' => 2,
                'status' => 'accepted',
                'changed_at' => now()->subDays(12)->setTime(1, 3),
                'changed_by' => 1
            ],
            [
                'order_id' => 8,
                'status' => 'preparing',
                'changed_at' => now()->subDays(12)->setTime(1, 40),
                'changed_by' => 1
            ],
            [
                'order_id' => 8,
                'status' => 'prepared',
                'changed_at' => now()->subDays(12)->setTime(2, 45),
                'changed_by' => 1
            ],
            [
                'order_id' => 8,
                'status' => 'picked_up',
                'changed_at' => now()->subDays(12)->setTime(3, 12),
                'changed_by' => 1
            ],
        ];
        DB::statement('set foreign_key_checks = 0;');
        $user1->walletTransactions()->create([
            'user_data' => [
                'name' => $user1->name,
                'mobile' => $user1->mobile,
                'email' => $user1->email
            ],
            'type' => 'debit',
            'order_id' => 8,
            'amount' => $orders[7]['total_price'] + $orders[7]['delivery_fee'],
        ]);
        DB::statement('set foreign_key_checks = 1;');

        foreach ($orders as $order)
            Order::create($order);

        foreach ($items as $item)
            OrderItem::create($item);

        foreach ($options as $option)
            OrderItemOption::create($option);

        $status_logs = [
            ...$status_logs1,
            ...$status_logs2,
            ...$status_logs3,
            ...$status_logs4,
            ...$status_logs5,
            ...$status_logs6,
            ...$status_logs7,
            ...$status_logs8,
        ];

        foreach ($status_logs as $log)
            OrderStatusHistory::create($log);
    }
}
