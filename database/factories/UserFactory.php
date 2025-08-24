<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName('male'),
            'last_name' => fake()->lastName('male'),
            'email' => fake()->unique()->safeEmail(),
            'mobile' => $this->faker->regexify('09[345689][0-9]{7}'),
            'status' => $this->faker->randomElement([0, 1]),
            'image' => 'default.png',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'created_at' => now()->subDays($this->faker->numberBetween(15, 60)),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
