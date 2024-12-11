<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    #[ArrayShape(['order_number' => "string", 'status' => "mixed"])] public function definition(): array
    {
        return [
            'order_number' => $this->faker->unique()->numerify('#####'),
            'status' => $this->faker->randomElement(['pending', 'shipped']),
            'total_amount' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
