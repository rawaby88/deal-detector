<?php

namespace Database\Factories\Order;

use App\Models\Order\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'ordered_at' => Carbon::now()->subDays($this->faker->numberBetween(0, 30)),
            'total_price_in_cents' => $this->faker->numberBetween(1000, 100000),
        ];
    }

    public function orderedAt(Carbon $date): Factory
    {
        return $this->state(function () use ($date) {
            return [
                'ordered_at' => $date,
            ];
        });
    }

    public function recent(): Factory
    {
        return $this->state(function () {
            return [
                'ordered_at' => Carbon::now()->subDays($this->faker->numberBetween(0, 7)),
            ];
        });
    }

    public function older(): Factory
    {
        return $this->state(function () {
            return [
                'ordered_at' => Carbon::now()->subDays($this->faker->numberBetween(30, 90)),
            ];
        });
    }
}
