<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WalletRechargeRequest>
 */
class WalletRechargeRequestFactory extends Factory
{
    protected static $status_array;
    protected static $min_and_max_nums_array;

    public static function setStatusArray($array)
    {
        static::$status_array = $array;
    }

    public static function setMinAndMaxNumsArray($array)
    {
        static::$min_and_max_nums_array = $array;
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        return [
            'user_id' => $user->id,
            'user_data' => [
                'name' => $user->name,
                'mobile' => $user->mobile,
                'email' => $user->email,
            ],
            'status' => $this->faker->randomElement(static::$status_array),
            'amount' => $this->faker->numberBetween(10000, 100000),
            'transfer_method' => $this->faker->randomElement(['cash', 'bank']),
            'proof_image' => 'receipt.jpeg',
            'note' => $this->faker->sentence(),
            'created_at' => now()->subDays(
                $this->faker->numberBetween(static::$min_and_max_nums_array['min'], static::$min_and_max_nums_array['max'])
            ),
        ];
    }
}
