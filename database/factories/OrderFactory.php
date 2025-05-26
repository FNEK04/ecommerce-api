<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'order_number' => 'ORD-' . $this->faker->unique()->numerify('########'),
            'status' => $this->faker->randomElement(['pending_payment', 'paid', 'cancelled']),
            'total_amount' => $this->faker->randomFloat(2, 10, 1000),
            'payment_url' => 'payment/' . $this->faker->uuid(),
            'expires_at' => now()->addMinutes(2),
            'paid_at' => null,
        ];
    }
} 